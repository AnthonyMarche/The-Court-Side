<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Translation\TranslatableMessage;

class CategoryCrudController extends AbstractCrudController
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(new TranslatableMessage('entity.category', ['parameter' => 'value'], 'admin'))
            ->setEntityLabelInPlural(new TranslatableMessage('entity.categories', ['parameter' => 'value'], 'admin'))
            ->setPageTitle(
                'index',
                new TranslatableMessage(
                    'entity.listOfCategories',
                    ['parameter' => 'value'],
                    'admin'
                )
            )
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', new TranslatableMessage('entity.name', ['parameter' => 'value'], 'admin')),

            DateTimeField::new(
                'createdAt',
                new TranslatableMessage(
                    'entity.createdAt',
                    ['parameter' => 'value'],
                    'admin'
                )
            )
                ->setFormat('dd/MM/YYYY')
                ->onlyOnIndex(),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Category) {
            return;
        }

        $slug = $this->slugger->slug($entityInstance->getName());
        $entityInstance->setSlug($slug);

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function createEntity(string $entityFqcn): Category
    {
        $category = new Category();
        $category->setCreatedAt(new DateTime());

        return $category;
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Category) {
            return;
        }

        $slug = $this->slugger->slug($entityInstance->getName());
        $entityInstance->setSlug($slug);
        $entityInstance->setUpdatedAt(new DateTime());

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }
}
