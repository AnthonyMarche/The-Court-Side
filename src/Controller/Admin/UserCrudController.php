<?php

namespace App\Controller\Admin;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('utilisateur')
            ->setEntityLabelInPlural('utilisateurs')
            ->setPageTitle('index', ' Liste des %entity_label_plural%')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setEntityPermission('ROLE_SUPER_ADMIN');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('username', "Nom d'utilisateur"),
            EmailField::new('email', 'Email')
                ->setDisabled(),
            ChoiceField::new('roles', 'Role')
                ->setChoices([
                    'Administrateur' => 'ROLE_ADMIN',
                ])
                ->renderExpanded()
                ->allowMultipleChoices(),
            DateTimeField::new('createdAt', 'Créé le')
                ->setFormat('dd/MM/YYYY')
                ->onlyOnIndex(),
        ];
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof User) {
            return;
        }

        $entityInstance->setUpdatedAt(new DateTime());

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }
}
