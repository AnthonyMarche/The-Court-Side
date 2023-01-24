<?php

namespace App\Controller;

use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function search(Request $request, VideoRepository $videoRepository): Response
    {
        $searchForm = $this->createForm(SearchType::class);

        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $videos = $videoRepository->findVideosBySearch($searchForm->getData());

            return $this->render('home/search_results.html.twig', [
                'videos' => $videos,
                'search' => $searchForm->getData()
            ]);
        }

        return $this->render('_includes/_searchForm.html.twig', [
            'searchForm' => $searchForm->createView()
        ]);
    }
}
