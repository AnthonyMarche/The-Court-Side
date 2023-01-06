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
            ->setEntityLabelInSingular('vidéo')
            ->setEntityLabelInPlural('vidéos')
            ->setPageTitle('index', ' Liste des %entity_label_plural%')
            ->setSearchFields(['title', 'description', 'category.name'])
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre'),

            TextareaField::new('description', 'Description')
                ->setMaxLength(115),

            BooleanField::new('isPrivate', 'Visibilité'),

            AssociationField::new('category', 'Catégorie'),

            AssociationField::new('tag', 'Tag')
                ->onlyOnForms(),

            ImageField::new('url', 'Fichier vidéo')
                ->onlyWhenCreating()
                ->setBasePath(self::PATHVIDEO)
                ->setUploadDir('public/uploads/videos')
                ->setUploadedFileNamePattern(self::PATHVIDEO . '/[slug]-[timestamp].[extension]')
                ->setHelp('Fichiers .avi, .mp4, .ogg and .wbm'),

            ImageField::new('teaser', 'Ajouter un teaser')
                ->onlyWhenCreating()
                ->setBasePath(self::PATHTEASER)
                ->setUploadDir('public/uploads/teasers')
                ->setUploadedFileNamePattern(self::PATHTEASER . '/teaser-[slug]-[timestamp].[extension]'),

            DateTimeField::new('createdAt', 'Créé le')
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
            unlink($video);
        }

        $entityManager->remove($entityInstance);
        $entityManager->flush();
    }
}
