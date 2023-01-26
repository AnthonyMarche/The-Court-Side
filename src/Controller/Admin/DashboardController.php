<?php

namespace App\Controller\Admin;

use _PHPStan_980551bf2\Nette\Utils\DateTime;
use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Video;
use App\Repository\CategoryRepository;
use App\Repository\LikeRepository;
use App\Repository\UserRepository;
use App\Repository\VideoRepository;
use App\Services\StatsGraphs;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    private UserRepository $userRepository;
    private VideoRepository $videoRepository;
    private LikeRepository $likeRepository;
    private CategoryRepository $categoryRepository;
    private StatsGraphs $statsGraphs;
    public function __construct(
        UserRepository $userRepository,
        VideoRepository $videoRepository,
        LikeRepository $likeRepository,
        CategoryRepository $categoryRepository,
        StatsGraphs $statsGraphs,
    ) {
        $this->userRepository = $userRepository;
        $this->videoRepository = $videoRepository;
        $this->likeRepository = $likeRepository;
        $this->categoryRepository = $categoryRepository;
        $this->statsGraphs = $statsGraphs;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // --- GENERATION DES STATISTIQUES -- //

        // récupère tous les utilisateurs (via le UserRepository)
        $users = $this->userRepository->findAll();
        // récupère toutes les videos (via le VideoRepository)
        $videos = $this->videoRepository->findAll();
        // récupère le nombre de likes
        $likes = $this->likeRepository->findAll();
        // récupère le nombre d'utilisateurs enregistrés dans les 7 derniers jours (via le UserRepository)
        $usersFromPast7Days = $this->userRepository->getUsersRegisteredInPast7Days();
        // récupère le nombre d'utilisateurs enregistrés dans les 30 derniers jours (via le UserRepository)
        $usersFromPast30Days = $this->userRepository->getUsersRegisteredInPast30Days();
        // récupère le nombre de videos ajoutées dans les 7 derniers jours (via le VideoRepository)
        $videosFromPast7Days = $this->videoRepository->getVideosAddedInPast7Days();
        // récupère le nombre de videos ajoutées dans les 30 derniers jours (via le VideoRepository)
        $videosFromPast30Days = $this->videoRepository->getVideosAddedInPast30Days();
        // récupère le nombre de likes enregistrés dans les 7 derniers jours (via le LikeRepository)
        $likesFromPast7Days = $this->likeRepository->getLikesAddedInPast7Days();
        // récupère le nombre de likes enregistrés dans les 30 derniers jours (via le LikeRepository)
        $likesFromPast30Days = $this->likeRepository->getLikesAddedInPast30Days();
        // récupère les category et comptabilise leurs likes
        $mostLikedCategories = $this->statsGraphs->viewMostLikedCategories();
        // graph évolution des inscriptions sur douze mois
        $subscriptionChart = $this->statsGraphs->viewSubscriptionsEvolution();
        // graph évolution des likes sur douze mois
        $likesChart = $this->statsGraphs->viewLikesEvolution();

        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'videos' => $videos,
            'likes' => $likes,
            'users_from_past_seven_days' => $usersFromPast7Days,
            'users_from_past_thirty_days' => $usersFromPast30Days,
            'videos_from_past_seven_days' => $videosFromPast7Days,
            'videos_from_past_thirty_days' => $videosFromPast30Days,
            'likes_from_past_seven_days' => $likesFromPast7Days,
            'likes_from_past_thirty_days' => $likesFromPast30Days,
            'most_liked_categories_chart' => $mostLikedCategories,
            'subscription_chart' => $subscriptionChart,
            'likes_chart' => $likesChart,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTranslationDomain('admin')
            ->setTitle('THE COURT SIDE | Admin');
    }

    /**
     * @SuppressWarnings(PHPMD)
     */
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard(
            new TranslatableMessage('dashboard.dashboard', ['parameter' => 'value'], 'admin'),
            'fa fa-home'
        );

        yield MenuItem::subMenu(
            new TranslatableMessage(
                'entity.user',
                ['parameter' => 'value'],
                'admin'
            ),
            'fa fa-user'
        )
            ->setSubItems([
            MenuItem::linkToCrud(
                new TranslatableMessage(
                    'dashboard.usersList',
                    ['parameter' => 'value'],
                    'admin'
                ),
                'fa fa-eye',
                User::class
            ),
            MenuItem::linkToRoute(
                new TranslatableMessage(
                    'dashboard.export',
                    ['parameter' => 'value'],
                    'admin'
                ),
                'fa fa-file-export',
                'download_users'
            )
        ])
            ->setPermission('ROLE_SUPER_ADMIN');

        yield MenuItem::subMenu(
            new TranslatableMessage(
                'entity.video',
                ['parameter' => 'value'],
                'admin'
            ),
            'fa fa-video'
        )
            ->setSubItems([
            MenuItem::linkToCrud(
                new TranslatableMessage(
                    'dashboard.videosList',
                    ['parameter' => 'value'],
                    'admin'
                ),
                'fa fa-eye',
                Video::class
            )
                ->setAction(Crud::PAGE_INDEX),
            MenuItem::linkToCrud(
                new TranslatableMessage(
                    'dashboard.newVideo',
                    ['parameter' => 'value'],
                    'admin'
                ),
                'fa fa-plus',
                Video::class
            )
                ->setAction(Crud::PAGE_NEW),
        ]);

        yield MenuItem::subMenu(
            new TranslatableMessage(
                'entity.category',
                ['parameter' => 'value'],
                'admin'
            ),
            'fa fa-list'
        )
            ->setSubItems([
            MenuItem::linkToCrud(
                new TranslatableMessage(
                    'dashboard.categoriesList',
                    ['parameter' => 'value'],
                    'admin'
                ),
                'fa fa-eye',
                Category::class
            )
                ->setAction(Crud::PAGE_INDEX),
            MenuItem::linkToCrud(
                new TranslatableMessage(
                    'dashboard.newCategory',
                    ['parameter' => 'value'],
                    'admin'
                ),
                'fa fa-plus',
                Category::class
            )
                ->setAction(Crud::PAGE_NEW),
        ]);

        yield MenuItem::subMenu(new TranslatableMessage('entity.tag', ['parameter' => 'value'], 'admin'), 'fa fa-tag')
            ->setSubItems([
                MenuItem::linkToCrud(
                    new TranslatableMessage(
                        'dashboard.tagsList',
                        ['parameter' => 'value'],
                        'admin'
                    ),
                    'fa fa-eye',
                    Tag::class
                )
                    ->setAction(Crud::PAGE_INDEX),
                MenuItem::linkToCrud(
                    new TranslatableMessage(
                        'dashboard.newTag',
                        ['parameter' => 'value'],
                        'admin'
                    ),
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
            new TranslatableMessage(
                'dashboard.leaveAdministration',
                ['parameter' => 'value'],
                'admin'
            ),
            "fa-solid fa-arrow-right-from-bracket",
            'app_home'
        );
    }
}
