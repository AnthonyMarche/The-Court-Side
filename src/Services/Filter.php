<?php

namespace App\Services;

use App\Repository\CategoryRepository;
use App\Repository\LikeRepository;
use Doctrine\DBAL\Exception;

class Filter
{
    private VideoRepository $videoRepository;

    /**
     * @param VideoRepository $videoRepository
     */
    public function __construct(VideoRepository $videoRepository)
    {
        $this->videoRepository = $videoRepository;
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

    public function getOrderedCategoryVideos(string $filter, string $slug): array
    {
        if ($filter == 'views') {
            return $this->videoRepository->findCategoryVideosOrderByViews($slug);
        } elseif ($filter == 'likes') {
            return $this->videoRepository->findCategoryVideosOrderByLikes($slug);
        } else {
            return $this->videoRepository->findCategoryVideosOrderByDate($slug);
        }
    }
}
