<?php

namespace App\Services;

use App\Repository\LikeRepository;
use App\Repository\VideoRepository;

class Filter
{
    private VideoRepository $videoRepository;
    private LikeRepository $likeRepository;

    public function __construct(VideoRepository $videoRepository, LikeRepository $likeRepository)
    {
        $this->videoRepository = $videoRepository;
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

    public function getOrderedTagVideos(string $filter, string $slug): array
    {
        if ($filter == 'views') {
            return $this->videoRepository->findTagVideosOrderByViews($slug);
        } elseif ($filter == 'likes') {
            return $this->videoRepository->findTagVideosOrderByLikes($slug);
        } else {
            return $this->videoRepository->findTagVideosOrderByDate($slug);
        }
    }
}
