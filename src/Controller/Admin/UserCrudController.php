<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Services\ExportUsers;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

#[IsGranted('ROLE_SUPER_ADMIN')]
class UserCrudController extends AbstractCrudController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular(new TranslatableMessage('entity.user', ['parameter' => 'value'], 'admin'))
            ->setEntityLabelInPlural(new TranslatableMessage('entity.users', ['parameter' => 'value'], 'admin'))
            ->setPageTitle(
                'index',
                new TranslatableMessage(
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
                new TranslatableMessage(
                    'entity.username',
                    ['parameter' => 'value'],
                    'admin'
                )
            ),
            EmailField::new('email', 'Email')
                ->setDisabled(),
            ChoiceField::new(
                'roles',
                new TranslatableMessage(
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

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof User) {
            return;
        }

        $entityInstance->setUpdatedAt(new DateTime());

        $entityManager->persist($entityInstance);
        $entityManager->flush();
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters
    ): QueryBuilder {
        $entityManager = $this->doctrine->getManager();
        return $entityManager->createQueryBuilder()
            ->select('u')
            ->from($entityDto->getFqcn(), 'u')
            ->Where('u.roles LIKE :roles')
            ->setParameter('roles', '%[]%')
            ->orWhere('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_ADMIN%');
    }

    #[Route('/users-csv', name: 'download_users')]
    public function downloadFile(ExportUsers $exportUsers): Response
    {
        // Create and get csv file
        $exportUsers->createCsvFile();
        $file = file_get_contents('registered-users.csv');

        // Define title and type of file
        $response = new Response($file);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'filename="Registered-users.csv"');

        return $response;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ;
    }
}
