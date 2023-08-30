<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Video;
use App\Services\Statistics;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Translation\TranslatableMessage;

class DashboardController extends AbstractDashboardController
{
    private Statistics $statistics;

    public function __construct(
        Statistics $statistics
    ) {
        $this->statistics = $statistics;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $statistics = $this->statistics->getStatistics();
        $registeredUserByMonth = $this->statistics->getRegisteredUserByMonth();
        $likeByMonth = $this->statistics->getNumberOfLikeByMonth();
        $mostLikedCategories = $this->statistics->getMostLikedCategory();

        return $this->render('admin/index.html.twig', [
            'statistics' => $statistics,
            'registeredUserByMonth' => $registeredUserByMonth,
            'likeByMonth' => $likeByMonth,
            'mostLikedCategories' => $mostLikedCategories
        ]);
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return UserMenu::new()
            ->setName($user->getUsername());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTranslationDomain('admin')
            ->setTitle('THE COURT SIDE | Admin')
            ->setFaviconPath('build/images/TCS_logo/TCS_favicon.png');
    }

    public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addWebpackEncoreEntry('admin');
    }

    /**
     * @SuppressWarnings(PHPMD)
     */
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard(
            new TranslatableMessage('dashboard.dashboard', [], 'admin'),
            'fa fa-home'
        );

        yield MenuItem::subMenu(
            new TranslatableMessage('entity.user', [], 'admin'),
            'fa fa-user'
        )
            ->setSubItems([
                MenuItem::linkToCrud(
                    new TranslatableMessage('dashboard.usersList', [], 'admin'),
                    'fa fa-eye',
                    User::class
                )
                    ->setPermission('ROLE_ADMIN'),
                MenuItem::linkToRoute(
                    new TranslatableMessage('dashboard.export', [], 'admin'),
                    'fa fa-file-export',
                    'download_users'
                )
            ]);

        yield MenuItem::subMenu(
            new TranslatableMessage('entity.video', [], 'admin'),
            'fa fa-video'
        )
            ->setSubItems([
                MenuItem::linkToCrud(
                    new TranslatableMessage('dashboard.videosList', [], 'admin'),
                    'fa fa-eye',
                    Video::class
                )
                    ->setAction(Crud::PAGE_INDEX),
                MenuItem::linkToCrud(
                    new TranslatableMessage('dashboard.newVideo', [], 'admin'),
                    'fa fa-plus',
                    Video::class
                )
                    ->setAction(Crud::PAGE_NEW),
                MenuItem::linkToRoute('Teaser', 'fa-sharp fa-solid fa-file-video', 'app_teaser_new')
            ]);

        yield MenuItem::subMenu(
            new TranslatableMessage('entity.category', [], 'admin'),
            'fa fa-list'
        )
            ->setSubItems([
                MenuItem::linkToCrud(
                    new TranslatableMessage('dashboard.categoriesList', [], 'admin'),
                    'fa fa-eye',
                    Category::class
                )
                    ->setAction(Crud::PAGE_INDEX),
                MenuItem::linkToCrud(
                    new TranslatableMessage('dashboard.newCategory', [], 'admin'),
                    'fa fa-plus',
                    Category::class
                )
                    ->setAction(Crud::PAGE_NEW),
            ]);

        yield MenuItem::subMenu(new TranslatableMessage('entity.tag', [], 'admin'), 'fa fa-tag')
            ->setSubItems([
                MenuItem::linkToCrud(
                    new TranslatableMessage('dashboard.tagsList', [], 'admin'),
                    'fa fa-eye',
                    Tag::class
                )
                    ->setAction(Crud::PAGE_INDEX),
                MenuItem::linkToCrud(
                    new TranslatableMessage('dashboard.newTag', [], 'admin'),
                    'fa fa-plus',
                    Tag::class
                )
                    ->setAction(Crud::PAGE_NEW),
            ]);

        yield MenuItem::linkToRoute(
            "Newsletter",
            "fa-regular fa-envelope",
            'app_newsletter'
        );

        yield MenuItem::linkToRoute(
            new TranslatableMessage('dashboard.leaveAdministration', [], 'admin'),
            "fa-solid fa-arrow-right-from-bracket",
            'app_home'
        );
    }
}
