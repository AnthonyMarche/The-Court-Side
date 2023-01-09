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
use function Symfony\Component\Translation\t;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(t('entity.user', ['parameter' => 'value'], 'admin'))
            ->setEntityLabelInPlural(t('entity.users', ['parameter' => 'value'], 'admin'))
            ->setPageTitle(
                'index',
                t(
                    'entity.listOfUsers',
                    ['parameter' => 'value'],
                    'admin'
                )
            )
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setEntityPermission('ROLE_SUPER_ADMIN');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'ID')
                ->hideOnForm(),
            TextField::new(
                'username',
                t(
                    'entity.username',
                    ['parameter' => 'value'],
                    'admin'
                )
            ),
            EmailField::new('email', 'Email')
                ->setDisabled(),
            ChoiceField::new(
                'roles',
                t(
                    'entity.role',
                    ['parameter' => 'value'],
                    'admin'
                )
            )
                ->setChoices([
                    'Admin' => 'ROLE_ADMIN',
                ])
                ->renderExpanded()
                ->allowMultipleChoices(),
            DateTimeField::new(
                'createdAt',
                t(
                    'entity.createdAt',
                    ['parameter' => 'value'],
                    'admin'
                )
            )
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
