<?php

namespace App\Controller\Admin;

use App\Entity\Tag;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\String\Slugger\SluggerInterface;

class TagCrudController extends AbstractCrudController
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public static function getEntityFqcn(): string
    {
        return Tag::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('tag')
            ->setEntityLabelInPlural('tags')
            ->setPageTitle('index', ' Liste des %entity_label_plural%')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom'),

            DateTimeField::new('createdAt', 'Créé le')
                ->setFormat('dd/MM/YYYY')
                ->onlyOnIndex(),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Tag) {
            return;
        }

        $slug = $this->slugger->slug($entityInstance->getName());
        $entityInstance->setSlug($slug);

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function createEntity(string $entityFqcn): Tag
    {
        $tag = new Tag();
        $tag->setCreatedAt(new DateTime());

        return $tag;
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Tag) {
            return;
        }

        $slug = $this->slugger->slug($entityInstance->getName());
        $entityInstance->setSlug($slug);
        $entityInstance->setUpdatedAt(new DateTime());

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }
}
