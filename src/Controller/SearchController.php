<?php

namespace App\Controller;

use App\Repository\VideoRepository;
use App\Services\Filter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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
            $url = $urlGenerator->generate('app_search_results', [
                'search' => $searchForm->getData(),
                'sortedBy' => 'recent'
            ]);

            return $this->redirect($url);
        }

        return $this->render('_includes/_searchForm.html.twig', [
            'searchForm' => $searchForm->createView()
        ]);
    }

    #[Route('/results/{search}', name: 'app_search_results')]
    public function searchResults(
        string $search,
        Request $request,
        Filter $filter,
        VideoRepository $videoRepository
    ): Response {
        $sortByRequest = $request->query->get('sortedBy');

        if (!$filter->isAllowedFilter($sortByRequest)) {
            throw new BadRequestHttpException(HomeController::INVALID_FILTER);
        }

        $sortBy = $filter->getMappedField($sortByRequest);
        $videos = $videoRepository->getVideoBySearch($search, $sortBy);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'content' => $this->renderView(HomeController::VIDEO_TEMPLATE, [
                    'videos' => $videos,
                ])
            ]);
        }

        return $this->render('home/search_results.html.twig', [
            'videos' => $videos,
            'search' => $search
        ]);
    }
}
