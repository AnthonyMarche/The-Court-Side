<?php

namespace App\Controller\Admin;

use App\Entity\Tag;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Translation\TranslatableMessage;

class TagCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tag::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(new TranslatableMessage('entity.tag', [], 'admin'))
            ->setEntityLabelInPlural(new TranslatableMessage('entity.tags', [], 'admin'))
            ->setPageTitle(
                'index',
                new TranslatableMessage('entity.listOfTags', [], 'admin')
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
