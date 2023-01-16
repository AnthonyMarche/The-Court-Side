<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Video;
use App\Repository\CategoryRepository;
use App\Repository\LikeRepository;
use App\Repository\UserRepository;
use App\Repository\VideoRepository;
use App\Services\Filter;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(name: 'app_')]
class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
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

    #[Route('/watch/{id}', name: 'watch')]
    public function watch(Video $video): Response
    {
        return $this->render('home/watch.html.twig', [
            'video' => $video
        ]);
    }

    #[Route('watch/{id}/like', name: 'watch_like', methods: ['POST', 'GET'])]
    public function addToLike(Video $video, UserRepository $userRepository): JsonResponse
    {

        /** @var \App\Entity\User */
        $user = $this->getUser();
        if ($user->isLiked($video)) {
            $user->removeLikedVideo($video);
        } else {
            $user->addLikedVideo($video);
        }

        $userRepository->save($user, true);

        return $this->json([
            'isLiked' => $user->isLiked($video)
        ]);
    }

    #[Route('/category', name: 'category')]
    public function showCategory(CategoryRepository $categoryRepository): Response
    {
        return $this->render('home/category.html.twig', [
            'categories' => $categoryRepository->findBy([], ['name' => 'ASC'])
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/likes/{sort}', name: 'likes')]
    public function showLikes(
        Filter $filter,
        Request $request,
        string $sort = 'recent'
    ): Response {

        $likedVideos = "";

        //injection security
        if (!$filter->preventInjection($sort)) {
            throw $this->createNotFoundException('filtre invalide');
        }

        //get videos liked by the current user
        if ($this->getUser()) {
            $currentUserId = $this->getUser()->getId();
            $likedVideos = $filter->getOrderedLikedVideos($sort, $currentUserId);

            //handle ajax request
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse([
                    'content' => $this->renderView('_includes/_liked_videos.html.twig', [
                        'likedVideos' => $filter->getOrderedLikedVideos($sort, $currentUserId),
                    ])
                ]);
            }
        }

        return $this->render('home/likes.html.twig', [
            'likedVideos' => $likedVideos
        ]);
    }

    #[Route('/Language/{language}/{route}', name: 'language')]
    public function changeLanguage(string $language, string $route): Response
    {
        return $this->redirectToRoute($route, ['_locale' => $language]);
    }

    /**
     * @throws Exception
     */
    #[Route('/category/{slug}/{sort}', name: 'single_category', methods: ['GET'])]
    public function showSingleCategory(
        Request $request,
        Filter $filter,
        string $slug,
        string $sort = 'recent'
    ): Response {

        //injection security
        if (!$filter->preventInjection($sort)) {
            throw $this->createNotFoundException('filtre invalide');
        }

        //handle ajax request
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'content' => $this->renderView('_includes/_videos_grid.html.twig', [
                    'videos' => $filter->getOrderedCategoryVideos($sort, $slug),
                ])
            ]);
        }

        return $this->render('home/singleCategory.html.twig', [
            'videos' => $filter->getOrderedCategoryVideos($sort, $slug),
        ]);
    }
}
