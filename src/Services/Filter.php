<?php

namespace App\Services;

use App\Repository\VideoRepository;

class Filter
{
    private VideoRepository $videoRepository;

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
        return $this->videoRepository->findOrderedVideosLikedByCurrentUser($filter, $currentUserId);
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
        return $this->videoRepository->findOrderedTagVideos($filter, $slug);
    }
}
