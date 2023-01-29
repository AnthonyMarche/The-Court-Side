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
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Translation\TranslatableMessage;

#[IsGranted('ROLE_ADMIN')]
class VideoCrudController extends AbstractCrudController
{
    private const PATH_VIDEO = 'uploads/videos';
    private const PATH_TEASER = 'uploads/teasers';
    private const PATH_NEW_TEASER = 'uploads/teasersCreated/';

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
                ->setBasePath(self::PATH_VIDEO)
                ->setUploadDir('public/uploads/videos')
                ->setUploadedFileNamePattern(self::PATH_VIDEO . '/[slug]-[timestamp].[extension]')
                ->setHelp(new TranslatableMessage('file.extensions', ['parameter' => 'value'], 'admin')),

            ImageField::new('teaser', new TranslatableMessage('entity.teaser', ['parameter' => 'value'], 'admin'))
                ->onlyOnForms()
                ->setBasePath(self::PATH_TEASER)
                ->setUploadDir('public/uploads/teasers')
                ->setUploadedFileNamePattern(self::PATH_TEASER . '/teaser-[slug]-[timestamp].[extension]')
                ->setHelp(new TranslatableMessage('file.extensions', ['parameter' => 'value'], 'admin')),

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
        if ($teaser) {
            unlink($teaser);
        }

        $entityManager->remove($entityInstance);
        $entityManager->flush();
    }
}
