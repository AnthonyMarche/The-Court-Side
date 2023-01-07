<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Video;
use App\Repository\UserRepository;
use App\Repository\VideoRepository;
use App\Services\Stats;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\Translation\t;

class DashboardController extends AbstractDashboardController
{
    private UserRepository $userRepository;
    private VideoRepository $videoRepository;

    public function __construct(UserRepository $userRepository, VideoRepository $videoRepository)
    {
        $this->userRepository = $userRepository;
        $this->videoRepository = $videoRepository;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // --- GENERATION DES STATISTIQUES -- //

        // récupère tous les utilisateurs (via le UserRepository)
        $users = $this->userRepository->findAll();
        // récupère toutes les videos (via le VideoRepository)
        $videos = $this->videoRepository->findAll();
        // récupère le nombre de likes (via service Stats)
        $likes = new Stats($this->videoRepository);
        $likes = $likes->getAllLikes();
        // récupère le nombre d'utilisateurs enregistrés dans les 7 derniers jours (via le UserRepository)
        $usersFromPast7Days = $this->userRepository->getUsersRegisteredInPast7Days();
        // récupère le nombre d'utilisateurs enregistrés dans les 30 derniers jours (via le UserRepository)
        $usersFromPast30Days = $this->userRepository->getUsersRegisteredInPast30Days();
        // récupère le nombre de videos ajoutées dans les 7 derniers jours (via le VideoRepository)
        $videosFromPast7Days = $this->videoRepository->getVideosAddedInPast7Days();
        // récupère le nombre de videos ajoutées dans les 30 derniers jours (via le VideoRepository)
        $videosFromPast30Days = $this->videoRepository->getVideosAddedInPast30Days();

        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'videos' => $videos,
            'likes' => $likes,
            'users_from_past_seven_days' => $usersFromPast7Days,
            'users_from_past_thirty_days' => $usersFromPast30Days,
            'videos_from_past_seven_days' => $videosFromPast7Days,
            'videos_from_past_thirty_days' => $videosFromPast30Days,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTranslationDomain('admin')
            ->setTitle('THE COURT SIDE | Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard(t('dashboard.dashboard', ['parameter' => 'value'], 'admin'), 'fa fa-home');

        yield MenuItem::linkToCrud(t('entity.user', ['parameter' => 'value'], 'admin'), 'fa fa-user', User::class)
            ->setPermission('ROLE_SUPER_ADMIN');

        yield MenuItem::subMenu(t('entity.video', ['parameter' => 'value'], 'admin'), 'fa fa-video')->setSubItems([
            MenuItem::linkToCrud(t('dashboard.videosList', ['parameter' => 'value'], 'admin'), 'fa fa-eye', Video::class)->setAction(Crud::PAGE_INDEX),
            MenuItem::linkToCrud(t('dashboard.newVideo', ['parameter' => 'value'], 'admin'), 'fa fa-plus', Video::class)->setAction(Crud::PAGE_NEW),
        ]);

        yield MenuItem::subMenu(t('entity.category', ['parameter' => 'value'], 'admin'), 'fa fa-list')->setSubItems([
            MenuItem::linkToCrud(t('dashboard.categoriesList', ['parameter' => 'value'], 'admin'), 'fa fa-eye', Category::class)->setAction(Crud::PAGE_INDEX),
            MenuItem::linkToCrud(t('dashboard.newCategory', ['parameter' => 'value'], 'admin'), 'fa fa-plus', Category::class)->setAction(Crud::PAGE_NEW),
        ]);

        yield MenuItem::subMenu(t('entity.tag', ['parameter' => 'value'], 'admin'), 'fa fa-tag')->setSubItems([
            MenuItem::linkToCrud(t('dashboard.tagsList', ['parameter' => 'value'], 'admin'), 'fa fa-eye', Tag::class)->setAction(Crud::PAGE_INDEX),
            MenuItem::linkToCrud(t('dashboard.newTag', ['parameter' => 'value'], 'admin'), 'fa fa-plus', Tag::class)->setAction(Crud::PAGE_NEW),
        ]);

        yield MenuItem::linkToUrl(t('dashboard.leaveAdministration', ['parameter' => 'value'], 'admin'), "fa-solid fa-arrow-right-from-bracket", '/');
    }
}
