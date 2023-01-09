<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Video;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Repository\VideoRepository;
use App\Services\Filter;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/watch/{id}', name: 'app_watch')]
    public function watch(Video $video): Response
    {
        return $this->render('home/watch.html.twig', [
            'video' => $video
        ]);
    }

    #[Route('watch/{id}/like', name: 'app_watch_like', methods: ['POST', 'GET'])]
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

    #[Route('/category', name: 'app_category')]
    public function showCategory(CategoryRepository $categoryRepository): Response
    {
        return $this->render('home/category.html.twig', [
            'categories' => $categoryRepository->findBy([], ['name' => 'ASC'])
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route('/likes/{sort}', name: 'app_likes')]
    public function showLikes(
        Filter $filter,
        VideoRepository $videoRepository,
        Request $request,
        string $sort = 'recent'
    ): Response {
        //injection security
        $likedVideos = '';
        $allowedSorts = ['recent', 'likes', 'views'];
        (in_array($sort, $allowedSorts) ?: throw $this->createNotFoundException('filtre invalide'));

        //get the videos liked by the current user
        if ($this->getUser()) {
            /** @var \App\Entity\User */
            $user = $this->getUser();
            $likedVideos = $videoRepository->getLikedVideos($user->getId());
        }

        //handle ajax request
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'content' => $this->renderView('_includes/_liked_videos.html.twig', [
                    'likedVideos' => $filter->getOrderedLikedVideos($sort),
                ])
            ]);
        }

        return $this->render('home/likes.html.twig', [
            'likedVideos' => $likedVideos
        ]);
    }

    #[Route('/Language/{language}/{route}', name: 'app_language')]
    public function changeLanguage($language, $route): Response
    {
        return $this->redirectToRoute($route, ['_locale' => $language]);
    }

    #[Route('/category/{slug}/{sort}', name: 'single_category', methods: ['GET'])]
    public function showSingleCategory(Category $category, string $sort = 'recent'): Response
    {
        return $this->render('home/singleCategory.html.twig', [
            'category' => $category,
        ]);
    }
}
