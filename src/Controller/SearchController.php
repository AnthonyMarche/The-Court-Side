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
    private Filter $filter;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(Filter $filter, UrlGeneratorInterface $urlGenerator)
    {
        $this->filter = $filter;
        $this->urlGenerator = $urlGenerator;
    }

    #[Route('/search/{sort}/{search}', name: 'app_search')]
    public function search(
        Request $request,
        string $search = "",
        string $sort = 'recent'
    ): JsonResponse|Response {

        //injection security
        if (!$this->filter->preventInjection($sort)) {
            throw $this->createNotFoundException('filtre invalide');
        }

        $searchForm = $this->createForm(SearchType::class);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $url = $this->urlGenerator->generate('app_results', [
                'search' => $searchForm->getData(),
                'sort' => $sort
            ]);

            return $this->redirect($url);
        }

        //handle ajax request
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'content' => $this->renderView('_includes/_videos_grid.html.twig', [
                    'videos' => $this->filter->getOrderedVideosBySearch($sort, $search),
                ])
            ]);
        }

        return $this->render('_includes/_searchForm.html.twig', [
            'searchForm' => $searchForm->createView()
        ]);
    }

    #[Route('/results/{search}/{sort}', name: 'app_results')]
    public function searchResults(string $search, string $sort): Response
    {
        $videos = $this->filter->getOrderedVideosBySearch($sort, $search);
        return $this->render('home/search_results.html.twig', [
            'videos' => $videos,
            'search' => $search
        ]);
    }
}
