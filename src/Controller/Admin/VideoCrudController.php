<?php

namespace App\Controller\Admin;

use App\Entity\Video;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class VideoCrudController extends AbstractCrudController
{
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

            DateTimeField::new('createdAt', 'Créé le')
                ->setFormat('dd/MM/YYYY'),
        ];
    }
}
