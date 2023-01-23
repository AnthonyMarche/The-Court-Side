<?php

namespace App\Controller\Admin;

use App\Entity\Video;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Translation\TranslatableMessage;

class VideoCrudController extends AbstractCrudController
{
    private const PATHVIDEO = 'uploads/videos';
    private const PATHTEASER = 'uploads/teasers';

    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public static function getEntityFqcn(): string
    {
        return Video::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->overrideTemplate('crud/new', 'admin/new.html.twig')
            ->setEntityLabelInSingular(new TranslatableMessage('entity.video', ['parameter' => 'value'], 'admin'))
            ->setEntityLabelInPlural(new TranslatableMessage('entity.videos', ['parameter' => 'value'], 'admin'))
            ->setPageTitle(
                'index',
                new TranslatableMessage(
                    'entity.listOfVideos',
                    ['parameter' => 'value'],
                    'admin'
                )
            )
            ->setSearchFields(['title', 'description', 'category.name'])
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new(
                'title',
                new TranslatableMessage(
                    'entity.title',
                    ['parameter' => 'value'],
                    'admin'
                )
            ),

            TextareaField::new(
                'description',
                new TranslatableMessage(
                    'entity.description',
                    ['parameter' => 'value'],
                    'admin'
                )
            )
                ->setMaxLength(115),

            BooleanField::new(
                'isPrivate',
                new TranslatableMessage(
                    'entity.visibility',
                    ['parameter' => 'value'],
                    'admin'
                )
            ),

            AssociationField::new(
                'category',
                new TranslatableMessage(
                    'entity.category',
                    ['parameter' => 'value'],
                    'admin'
                )
            ),

            AssociationField::new(
                'tag',
                new TranslatableMessage(
                    'entity.tag',
                    ['parameter' => 'value'],
                    'admin'
                )
            )
                ->onlyOnForms(),

            ImageField::new('url', new TranslatableMessage('entity.file', ['parameter' => 'value'], 'admin'))
                ->onlyWhenCreating()
                ->setBasePath(self::PATHVIDEO)
                ->setUploadDir('public/uploads/videos')
                ->setUploadedFileNamePattern(self::PATHVIDEO . '/[slug]-[timestamp].[extension]')
                ->setHelp('Fichiers .avi, .mp4, .ogg and .wbm'),

            ImageField::new('teaser', new TranslatableMessage('entity.teaser', ['parameter' => 'value'], 'admin'))
                ->onlyWhenCreating()
                ->setBasePath(self::PATHTEASER)
                ->setUploadDir('public/uploads/teasers')
                ->setUploadedFileNamePattern(self::PATHTEASER . '/teaser-[slug]-[timestamp].[extension]'),

            DateTimeField::new(
                'createdAt',
                new TranslatableMessage(
                    'entity.createdAt',
                    ['parameter' => 'value'],
                    'admin'
                )
            )
                ->onlyOnIndex()
                ->setFormat('dd/MM/YYYY'),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Video) {
            return;
        }

        /** @var \App\Entity\User */
        $user = $this->getUser();
        $entityInstance->setUser($user);

        if (!$entityInstance->getTeaser()) {
            $entityInstance->setTeaser($entityInstance->getUrl());
        }

        $slug = $this->slugger->slug($entityInstance->getTitle());
        $entityInstance->setSlug($slug);

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function createEntity(string $entityFqcn): Video
    {
        $video = new Video();

        $video->setNumberOfView(0);
        $video->setNumberOfLike(0);
        $video->setCreatedAt(new DateTime());

        return $video;
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {

        if (!$entityInstance instanceof Video) {
            return;
        }

        /** @var \App\Entity\User */
        $user = $this->getUser();
        $entityInstance->setUser($user);

        $slug = $this->slugger->slug($entityInstance->getTitle());
        $entityInstance->setSlug($slug);

        $entityInstance->setUpdatedAt(new DateTime());

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $video = $entityInstance->getUrl();
        $teaser = $entityInstance->getTeaser();

        unlink($video);
        if ($teaser != null) {
            unlink($teaser);
        }

        $entityManager->remove($entityInstance);
        $entityManager->flush();
    }
}
