<?php

namespace App\Services;

use App\Repository\VideoRepository;

class Stats
{
    private VideoRepository $videoRepository;

    public function __construct(VideoRepository $videoRepository)
    {
        $this->videoRepository = $videoRepository;
    }

    /**
     *  Get all likes from all videos
     *
     * @return int
     */
    public function getAllLikes(): int
    {
        $videos = $this->videoRepository->findAll();
        $likes = [];

        foreach ($videos as $video) {
            $likes[] = $video->getLikedByUser()->count();
        }

        return array_sum($likes);
    }
}
