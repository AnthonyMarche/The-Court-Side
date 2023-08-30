<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Translation\TranslatableMessage;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(new TranslatableMessage('entity.category', [], 'admin'))
            ->setEntityLabelInPlural(new TranslatableMessage('entity.categories', [], 'admin'))
            ->setPageTitle(
                'index',
                new TranslatableMessage('entity.listOfCategories', [], 'admin')
            )
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', new TranslatableMessage('entity.name', [], 'admin')),

            DateTimeField::new(
                'createdAt',
                new TranslatableMessage('entity.createdAt', [], 'admin')
            )
                ->setFormat('dd/MM/YYYY')
                ->onlyOnIndex(),
        ];
    }
}
