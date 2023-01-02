<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(VideoRepository $videoRepository): Response
    {
        $latestVideos = $videoRepository->findBy([], ['createdAt' => 'DESC'], 4);
        $popularVideos = $videoRepository->findBy([], ['numberOfView' => 'DESC'], 4);
        $tennisVideos = $videoRepository->findBy(['category' => 3], [], 4);
        $registerVideos = $videoRepository->findBy(['isPrivate' => true], [], 4);
        return $this->render('home/index.html.twig', [
            'latestVideos' => $latestVideos,
            'popularVideos' => $popularVideos,
            'tennisVideos' => $tennisVideos,
            'registerVideos' => $registerVideos
        ]);
    }

    #[Route('/category', name: 'app_category')]
    public function showCategory(CategoryRepository $categoryRepository): response
    {
        return $this->render('home/category.html.twig', [
            'categories' => $categoryRepository->findBy([], ['name' => 'ASC']),
        ]);
    }
}
