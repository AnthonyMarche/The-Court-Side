<?php

namespace App\Controller\Admin;

use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\File;

class VideoCrudController extends AbstractCrudController
{
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
                ->onlyOnForms()
                ->setBasePath('uploads/videos')
                ->setUploadDir('public/uploads/videos')
                ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
                ->setHelp('Fichiers .avi, .mp4, .ogg and .wbm'),

            //                ->setFormTypeOption('constraints', [
//                    new File([
//                        'mimeTypes' => [
//                            'video/AV1',
//                            'video/webm',
//                            'video/ogg',
//                            'video/mp4'
//                        ],
//                        'mimeTypesMessage' => 'Veuilez utiliser un fichier vidéo valide.',
//                        'disallowEmptyMessage' => 'Veuillez télécharger un fichier vidéo'
//                    ])
//                ]),


            ImageField::new('teaser', 'Ajouter un teaser')
                ->onlyOnForms()
                ->setBasePath('uploads/teasers')
                ->setUploadDir('public/uploads/teasers')
                ->setUploadedFileNamePattern('teaser-[slug]-[timestamp].[extension]'),

            DateTimeField::new('createdAt', 'Créé le')
                ->onlyOnIndex()
                ->setFormat('dd/MM/YYYY'),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Video) return;

        $entityInstance->setUser($this->getUser());

        $slug = $this->slugger->slug($entityInstance->getTitle());
        $entityInstance->setSlug($slug);

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function createEntity(string $entityFqcn): Video
    {
        $video = new Video();

        $video->setNumberOfView(0);
        $video->setCreatedAt(new \DateTime());

        return $video;
    }
}
