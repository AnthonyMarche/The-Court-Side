<?php

namespace App\Services;

use App\Repository\VideoRepository;

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
}
