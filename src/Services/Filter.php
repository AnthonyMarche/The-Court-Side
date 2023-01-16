<?php

namespace App\Services;

use App\Repository\CategoryRepository;
use App\Repository\VideoRepository;
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


    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getOrderedLikedVideos(string $filter): array
    {
        if ($filter == 'views') {
            return $this->videoRepository->getLikedVideosOrderByViews();
        } elseif ($filter == 'likes') {
            return $this->videoRepository->getLikedVideosOrderByLikes();
        } else {
            return $this->videoRepository->getLikedVideosOrderByDate();
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
