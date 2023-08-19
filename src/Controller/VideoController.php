<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Video;
use App\Repository\CategoryRepository;
use App\Repository\VideoRepository;
use App\Services\Filter;
use App\Services\LikeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route(name: 'app_')]
class VideoController extends AbstractController
{
    public const VIDEO_TEMPLATE = '_includes/_videos_grid.html.twig';
    public const INVALID_FILTER = 'Invalid filter';
    private VideoRepository $videoRepository;
    private Filter $filter;

    /**
     * @param VideoRepository $videoRepository
     * @param Filter $filter
     */
    public function __construct(VideoRepository $videoRepository, Filter $filter)
    {
        $this->videoRepository = $videoRepository;
        $this->filter = $filter;
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $latestVideos = $this->videoRepository->findBy([], ['createdAt' => 'DESC'], 4);
        $popularVideos = $this->videoRepository->findBy([], ['numberOfView' => 'DESC'], 4);
        $moreLikedVideos = $this->videoRepository->findBy([], ['numberOfLike' => 'DESC'], 4);
        $registerVideos = $this->videoRepository->findBy(['isPrivate' => true], ['createdAt' => 'DESC'], 4);
        return $this->render('home/index.html.twig', [
            'latestVideos' => $latestVideos,
            'popularVideos' => $popularVideos,
            'moreLikedVideos' => $moreLikedVideos,
            'registerVideos' => $registerVideos
        ]);
    }

    #[Route('/favorite', name: 'favorite')]
    public function showLikes(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $userId = $user->getId();

        $sortByRequest = $request->query->get('sortedBy');
        if (empty($sortByRequest) || !$this->filter->isAllowedFilter($sortByRequest)) {
            throw new BadRequestHttpException(self::INVALID_FILTER);
        }

        $sortBy = $this->filter->getMappedField($sortByRequest);
        $videos = $this->videoRepository->getLikedVideoByUser($userId, $sortBy);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'content' => $this->renderView(self::VIDEO_TEMPLATE, [
                    'videos' => $videos,
                ])
            ]);
        }

        return $this->render('home/likes.html.twig', [
            'videos' => $videos
        ]);
    }

    #[Route('/categories', name: 'categories')]
    public function showCategory(CategoryRepository $categoryRepository): Response
    {
        return $this->render('home/categories.html.twig', [
            'categories' => $categoryRepository->findBy([], ['name' => 'ASC'])
        ]);
    }

    #[Route('/category/{slug}', name: 'category', methods: ['GET'])]
    public function showSingleCategory(Request $request, string $slug): Response
    {
        $sortByRequest = $request->query->get('sortedBy');

        if (!$this->filter->isAllowedFilter($sortByRequest)) {
            throw new BadRequestHttpException(self::INVALID_FILTER);
        }

        $sortBy = $this->filter->getMappedField($sortByRequest);
        $videos = $this->videoRepository->getVideoByCategory($slug, $sortBy);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'content' => $this->renderView(self::VIDEO_TEMPLATE, [
                    'videos' => $videos,
                ])
            ]);
        }

        return $this->render('home/category.html.twig', [
            'videos' => $videos,
        ]);
    }

    #[Route('/private-videos', name: 'private_videos')]
    public function showPrivateVideos(Request $request): Response
    {
        $sortByRequest = $request->query->get('sortedBy');

        if (!$this->filter->isAllowedFilter($sortByRequest)) {
            throw new BadRequestHttpException(self::INVALID_FILTER);
        }

        $sortBy = $this->filter->getMappedField($sortByRequest);
        $videos = $this->videoRepository->getPrivateVideo($sortBy);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'content' => $this->renderView(self::VIDEO_TEMPLATE, [
                    'videos' => $videos,
                ])
            ]);
        }

        return $this->render('home/privateVideos.html.twig', [
            'videos' => $videos
        ]);
    }

    #[Route('/video', name: 'video')]
    public function showAllVideos(Request $request): Response|JsonResponse
    {
        $sortByRequest = $request->query->get('sortedBy');

        if (!$this->filter->isAllowedFilter($sortByRequest)) {
            throw new BadRequestHttpException(self::INVALID_FILTER);
        }

        $sortBy = $this->filter->getMappedField($sortByRequest);
        $videos = $this->videoRepository->getVideoBySort($sortBy);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'content' => $this->renderView(self::VIDEO_TEMPLATE, [
                    'videos' => $videos,
                ])
            ]);
        }

        return $this->render('home/allVideos.html.twig', [
            'videos' => $videos
        ]);
    }

    #[Route('/tag/{slug}', name: 'tag', methods: ['GET'])]
    public function showSingleTag(Request $request, string $slug): Response
    {
        $sortByRequest = $request->query->get('sortedBy');

        if (!$this->filter->isAllowedFilter($sortByRequest)) {
            throw new BadRequestHttpException(self::INVALID_FILTER);
        }

        $sortBy = $this->filter->getMappedField($sortByRequest);
        $videos = $this->videoRepository->getVideoByTag($slug, $sortBy);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'content' => $this->renderView(self::VIDEO_TEMPLATE, [
                    'videos' => $videos,
                ])
            ]);
        }

        return $this->render('home/tag.html.twig', [
            'videos' => $videos,
            'tagName' => str_replace('-', ' ', $slug)
        ]);
    }

    #[Route('/watch/{slug}', name: 'watch')]
    public function watch(Video $video): Response
    {
        // Prevent a private video to be seen by an unsubscribed user
        if ($video->isIsPrivate() && !$this->getUser()) {
            throw $this->createAccessDeniedException(
                'Access denied, you should be registered to watch this video'
            );
        }

        // More videos from the same category
        $categoryId = $video->getCategory()->getId();
        $videoId = $video->getId();
        $moreVideos = $this->videoRepository->getSimilarVideosByCategory($categoryId, $videoId);

        return $this->render('home/watch.html.twig', [
            'video' => $video,
            'moreVideos' => $moreVideos,
        ]);
    }

    #[Route('like/{id}', name: 'like', methods: 'POST')]
    public function addToLike(Video $video, LikeService $likeService): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(
                ['error' => 'Vous devez être connectez pour aimer une vidéo'],
                400
            );
        }

        if ($video->isLikedByUser($user)) {
            $likeService->unlikeVideo($video, $user);
        } else {
            $likeService->likeVideo($video, $user);
        }

        return new JsonResponse([], 200);
    }
}
