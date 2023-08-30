<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Translation\TranslatableMessage;

class VideoCrudController extends AbstractCrudController
{
    private const PATH_VIDEO = 'uploads/videos';
    private const PATH_TEASER = 'uploads/teasers';

    public static function getEntityFqcn(): string
    {
        return Video::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->overrideTemplate('crud/new', 'admin/new.html.twig')
            ->setEntityLabelInSingular(new TranslatableMessage('entity.video', [], 'admin'))
            ->setEntityLabelInPlural(new TranslatableMessage('entity.videos', [], 'admin'))
            ->setPageTitle(
                'index',
                new TranslatableMessage('entity.listOfVideos', [], 'admin')
            )
            ->setSearchFields(['title', 'description', 'category.name'])
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new(
                'title',
                new TranslatableMessage('entity.title', [], 'admin')
            ),

            TextareaField::new(
                'description',
                new TranslatableMessage('entity.description', [], 'admin')
            )
                ->setMaxLength(115),

            BooleanField::new(
                'isPrivate',
                new TranslatableMessage('entity.visibility', [], 'admin')
            ),

            AssociationField::new(
                'category',
                new TranslatableMessage('entity.category', [], 'admin')
            ),

            AssociationField::new(
                'tag',
                new TranslatableMessage('entity.tag', [], 'admin')
            )
                ->onlyOnForms(),

            ImageField::new('url', new TranslatableMessage('entity.file', [], 'admin'))
                ->onlyWhenCreating()
                ->setBasePath(self::PATH_VIDEO)
                ->setUploadDir('public/uploads/videos')
                ->setUploadedFileNamePattern(self::PATH_VIDEO . '/[slug]-[timestamp].[extension]')
                ->setHelp(new TranslatableMessage('file.extensions', [], 'admin')),

            ImageField::new('teaser', new TranslatableMessage('entity.teaser', [], 'admin'))
                ->onlyOnForms()
                ->setBasePath(self::PATH_TEASER)
                ->setUploadDir('public/uploads/teasers')
                ->setUploadedFileNamePattern(self::PATH_TEASER . '/teaser-[slug]-[timestamp].[extension]')
                ->setHelp(new TranslatableMessage('file.extensions', [], 'admin')),

            DateTimeField::new(
                'createdAt',
                new TranslatableMessage('entity.createdAt', [], 'admin')
            )
                ->onlyOnIndex()
                ->setFormat('dd/MM/YYYY'),
        ];
    }

    public function createEntity(string $entityFqcn)
    {
        $video =  new $entityFqcn();

        /** @var User $user */
        $user = $this->getUser();
        $video->setUser($user);

        return $video;
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
