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
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\Translation\t;

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
//            ->overrideTemplate('crud/index', 'admin/index.html.twig')
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
}
