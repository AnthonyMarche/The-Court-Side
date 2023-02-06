<?php

namespace App\Controller;

use App\Services\Filter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function search(Request $request, UrlGeneratorInterface $urlGenerator): Response
    {
        $searchForm = $this->createForm(SearchType::class);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $url = $urlGenerator->generate('app_results', [
                'search' => $searchForm->getData(),
                'sort' => 'recent'
            ]);

            return $this->redirect($url);
        }

        return $this->render('_includes/_searchForm.html.twig', [
            'searchForm' => $searchForm->createView()
        ]);
    }

    #[Route('/results/{search}/{sort}', name: 'app_results')]
    public function searchResults(string $search, string $sort, Request $request, Filter $filter): Response
    {
        //injection security
        if (!$filter->preventInjection($sort)) {
            throw $this->createNotFoundException('filtre invalide');
        }

        //handle ajax request
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'content' => $this->renderView('_includes/_videos_grid.html.twig', [
                    'videos' => $filter->getOrderedVideosBySearch($sort, $search),
                ])
            ]);
        }

        $videos = $filter->getOrderedVideosBySearch($sort, $search);
        return $this->render('home/search_results.html.twig', [
            'videos' => $videos,
            'search' => $search
        ]);
    }
}
