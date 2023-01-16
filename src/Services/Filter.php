<?php

namespace App\Services;

use App\Repository\CategoryRepository;
use App\Repository\LikeRepository;
use Doctrine\DBAL\Exception;

class Filter
{
    private CategoryRepository $categoryRepository;
    private LikeRepository $likeRepository;


    public function __construct(CategoryRepository $categoryRepository, LikeRepository $likeRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->likeRepository = $likeRepository;
    }

    public function preventInjection(string $sort): bool
    {
        $allowedSorts = ['recent', 'likes', 'views'];
        if (in_array($sort, $allowedSorts)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @throws Exception
     */
    public function getOrderedLikedVideos(string $filter, int $currentUserId): array
    {
        if ($filter == 'recent') {
            return $this->likeRepository->findVideosLikedByCurrentUserOrderByDate($currentUserId);
        } elseif ($filter == 'views') {
            return $this->likeRepository->findVideosLikedByCurrentUserOrderByViews($currentUserId);
        } else {
            return $this->likeRepository->findVideosLikedByCurrentUserOrderByLikes($currentUserId);
        }
    }

    /**
     * @throws Exception
     */
    public function getOrderedCategoryVideos(string $filter, string $slug): array
    {
        if ($filter == 'views') {
            return $this->categoryRepository->getCategoryVideosOrderByViews($slug);
        } elseif ($filter == 'likes') {
            return $this->categoryRepository->getCategoryVideosOrderByLikes($slug);
        } else {
            return $this->categoryRepository->getCategoryVideosOrderByDate($slug);
        }
    }
}
