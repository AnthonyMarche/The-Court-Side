<?php

namespace App\Controller;

use App\Entity\Video;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Repository\VideoRepository;
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

//    #[Route('/Language/{language}/{route}', 'change_language')]
//    public function showLikes(Request $request, $language, $route): Response
//    {
//        dd($route);
//        $request->setLocale($language);
//
//        return $this->redirectToRoute();
//    }
}
