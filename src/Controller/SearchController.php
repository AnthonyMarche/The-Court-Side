<?php

namespace App\Controller;

use App\Services\Filter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/search/{sort}/{search}', name: 'app_search')]
    public function search(
        Request $request,
        Filter $filter,
        string $search = "",
        string $sort = 'recent'
    ): JsonResponse|Response {

        //injection security
        if (!$filter->preventInjection($sort)) {
            throw $this->createNotFoundException('filtre invalide');
        }

        $searchForm = $this->createForm(SearchType::class);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $search = $searchForm->getData();
            $videos = $filter->getOrderedVideosBySearch($sort, $search);

            return $this->render('home/search_results.html.twig', [
                'videos' => $videos,
                'search' => $searchForm->getData()
            ]);
        }

        //handle ajax request
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'content' => $this->renderView('_includes/_videos_grid.html.twig', [
                    'videos' => $filter->getOrderedVideosBySearch($sort, $search),
                ])
            ]);
        }

        return $this->render('_includes/_searchForm.html.twig', [
            'searchForm' => $searchForm->createView()
        ]);
    }
}
