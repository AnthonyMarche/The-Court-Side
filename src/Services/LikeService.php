<?php

namespace App\Services;

use App\Entity\Like;
use App\Entity\User;
use App\Entity\Video;
use App\Repository\LikeRepository;
use DateTime;

class LikeService
{
    private LikeRepository $likeRepository;

    /**
     * @param LikeRepository $likeRepository
     */
    public function __construct(LikeRepository $likeRepository)
    {
        $this->likeRepository = $likeRepository;
    }

    public function unlikeVideo(Video $video, User $user): void
    {
        $like = $this->likeRepository->getLikeByUserIdAndVideoId($user->getId(), $video->getId());
        $this->likeRepository->remove($like, true);
        $video->setNumberOfLike($video->getNumberOfLike() - 1);
    }

    public function likeVideo(Video $video, User $user): void
    {
        $like = new Like();
        $like->setVideo($video)
            ->setUser($user)
            ->setCreatedAt(new DateTime());
        $video->setNumberOfLike($video->getNumberOfLike() + 1);
        $this->likeRepository->save($like, true);
    }
}
