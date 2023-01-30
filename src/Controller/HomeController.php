<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Video;
use App\Repository\CategoryRepository;
use App\Repository\LikeRepository;
use App\Repository\VideoRepository;
use App\Services\Filter;
use DateTime;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        $moreLikedVideos = $videoRepository->findBy([], ['numberOfLike' => 'DESC'], 4);
        $registerVideos = $videoRepository->findBy(['isPrivate' => true], ['createdAt' => 'DESC'], 4);
        return $this->render('home/index.html.twig', [
            'latestVideos' => $latestVideos,
            'popularVideos' => $popularVideos,
            'moreLikedVideos' => $moreLikedVideos,
            'registerVideos' => $registerVideos
        ]);
    }

    #[Route('/watch/{slug}', name: 'watch')]
    public function watch(Video $video): Response
    {
        //prevent the video to be seen by an unsubscribed user
        if ($video->isIsPrivate() === true && $this->getUser() === null) {
            throw $this->createNotFoundException('access denied');
        }

        return $this->render('home/watch.html.twig', [
            'video' => $video
        ]);
    }

    #[Route('watch/{id}/like', name: 'watch_like', methods: ['GET', 'POST'])]
    public function addToLike(
        Video $video,
        EntityManagerInterface $manager,
        LikeRepository $likeRepository
    ): JsonResponse|RedirectResponse {

        /** @var \App\Entity\User */
        $user = $this->getUser();

        if ($this->getUser() === null) {
            $this->addFlash('warning', 'vous devez être connectez pour accéder a cette page');
            return $this->redirectToRoute('app_user_login');
        }

        if ($video->isLikedByUser($user)) {
            $like = $likeRepository->findOneBy(['video' => $video, 'user' => $user]);
            $video->setNumberOfLike($video->getNumberOfLike() - 1);
            $manager->remove($like);
            $manager->flush();

            return $this->json(['code' => 200], 200);
        }

        $like = new Like();
        $like->setVideo($video)
            ->setUser($user)
            ->setCreatedAt(new DateTime('now'));
        $video->setNumberOfLike($video->getNumberOfLike() + 1);
        $manager->persist($like);
        $manager->flush();

        return $this->json(['code' => 200], 200);
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
    #[Route('/favorite/{sort}', name: 'likes')]
    public function showLikes(
        Filter $filter,
        Request $request,
        string $sort
    ): Response {

        $likedVideos = "";

        //injection security
        if (!$filter->preventInjection($sort)) {
            throw $this->createNotFoundException('filtre invalide');
        }

        //get videos liked by the current user
        /** @var \App\Entity\User */
        $user = $this->getUser();
        if ($this->getUser()) {
            $currentUserId = $user->getId();
            $likedVideos = $filter->getOrderedLikedVideos($sort, $currentUserId);

            //handle ajax request
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse([
                    'content' => $this->renderView('_includes/_videos_grid.html.twig', [
                        'videos' => $filter->getOrderedLikedVideos($sort, $currentUserId),
                    ])
                ]);
            }
        }

        return $this->render('home/likes.html.twig', [
            'videos' => $likedVideos
        ]);
    }

    #[Route('/Language/{language}/{route}', name: 'language')]
    public function changeLanguage(string $language, string $route): Response
    {
        return $this->redirectToRoute($route, ['_locale' => $language]);
    }

    #[Route('/category/{slug}/{sort}', name: 'single_category', methods: ['GET'])]
    public function showSingleCategory(
        Request $request,
        Filter $filter,
        string $slug,
        string $sort
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

    #[Route('/tag/{slug}/{sort}', name: 'tag', methods: ['GET'])]
    public function showSingleTag(
        Request $request,
        Filter $filter,
        string $slug,
        string $sort
    ): Response {

        //injection security
        if (!$filter->preventInjection($sort)) {
            throw $this->createNotFoundException('filtre invalide');
        }

        //handle ajax request
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'content' => $this->renderView('_includes/_videos_grid.html.twig', [
                    'videos' => $filter->getOrderedTagVideos($sort, $slug),
                    'tagSlug' => $slug,
                    'tagName' => str_replace('-', ' ', $slug)
                ])
            ]);
        }
        return $this->render('home/singleTag.html.twig', [
            'videos' => $filter->getOrderedTagVideos($sort, $slug),
            'tagSlug' => $slug,
            'tagName' => str_replace('-', ' ', $slug)
        ]);
    }

    #[Route('/private/{sort}', name: 'private_videos')]
    public function showPrivateVideos(Request $request, Filter $filter, string $sort): Response
    {

        //injection security
        if (!$filter->preventInjection($sort)) {
            throw $this->createNotFoundException('filtre invalide');
        }

        //handle ajax request
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'content' => $this->renderView('_includes/_videos_grid.html.twig', [
                    'videos' => $filter->getOrderedPrivateVideos($sort),
                ])
            ]);
        }

        return $this->render('home/privateVideos.html.twig', [
            'videos' => $filter->getOrderedPrivateVideos($sort)
        ]);
    }
}

 #[Route('/all/{sort}', name: 'all')]
    public function showAllVideos(Request $request, Filter $filter, string $sort): Response|JsonResponse
    {
        //injection security
        if (!$filter->preventInjection($sort)) {
            throw $this->createNotFoundException('filtre invalide');
        }

        //handle ajax request
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'content' => $this->renderView('_includes/_videos_grid.html.twig', [
                    'videos' => $filter->getOrderedVideos($sort)
                ])
            ]);
        }

        return $this->render('home/allVideos.html.twig', [
            'videos' => $filter->getOrderedVideos($sort)
        ]);
    }
}
