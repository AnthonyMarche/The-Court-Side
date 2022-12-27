<?php

namespace App\Controller;

use App\Entity\Video;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/watch/{id}', name: 'app_watch', methods: ['GET'])]
    public function watch(Video $video): Response
    {
        return $this->render('home/watch.html.twig', [
            'video' => $video,
        ]);
    }
}
