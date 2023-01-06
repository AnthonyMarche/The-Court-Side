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
    public function getOrderedLikedVideos($filter, $user): array
    {
        if ($filter == 'views') {
            return $this->videoRepository->getVideosOrderByViews($user);
        } else if ($filter == 'likes') {
            return $this->videoRepository->getVideosOrderByLikes();
        } else {
            return $this->videoRepository->getVideosOrderByDate();
        }
    }
}
