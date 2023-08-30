<?php

namespace App\Services;

use App\Repository\CategoryRepository;
use App\Repository\LikeRepository;
use App\Repository\UserRepository;
use App\Repository\VideoRepository;
use DateTime;
use Symfony\Contracts\Translation\TranslatorInterface;

class Statistics
{
    private UserRepository $userRepository;
    private VideoRepository $videoRepository;
    private LikeRepository $likeRepository;
    private CategoryRepository $categoryRepository;
    private TranslatorInterface $translator;

    public function __construct(
        UserRepository $userRepository,
        VideoRepository $videoRepository,
        LikeRepository $likeRepository,
        CategoryRepository $categoryRepository,
        TranslatorInterface $translator
    ) {
        $this->userRepository = $userRepository;
        $this->videoRepository = $videoRepository;
        $this->likeRepository = $likeRepository;
        $this->categoryRepository = $categoryRepository;
        $this->translator = $translator;
    }

    public function getStatistics(): array
    {
        $fromOneWeek = new DateTime('-7 days');
        $fromOneMonth = new DateTime('-1 month');

        // Translate keys
        $globalStats = $this->translator->trans('stats.general');
        $sevenDaysAgo = $this->translator->trans('stats.last-7-days');
        $oneMonthAgo = $this->translator->trans('stats.last-30-days');

        return [
            $globalStats => $this->getStatsFromDate(),
            $sevenDaysAgo => $this->getStatsFromDate($fromOneWeek),
            $oneMonthAgo => $this->getStatsFromDate($fromOneMonth),
        ];
    }

    public function getStatsFromDate(DateTime $date = null): array
    {
        $newUser = $this->userRepository->countRegisteredUserFromDate($date);
        $lastVideoAdded = $this->videoRepository->getVideosAddedFromDate($date);
        $numberOfLike = $this->likeRepository->getNumberOfLikeFromDate($date);

        // Translate keys
        $registeredUserText = $this->translator->trans('stats.subs-nb');
        $newRegisteredUserText = $this->translator->trans('stats.new-subs');
        $numberOfVideosText = $this->translator->trans('stats.video-nb');
        $videoAddedText = $this->translator->trans('stats.new-videos');
        $likeNumberText = $this->translator->trans('stats.like-nb');

        return [
            $date ? $newRegisteredUserText : $registeredUserText => $newUser,
            $date ? $videoAddedText : $numberOfVideosText => $lastVideoAdded,
            $likeNumberText => $numberOfLike
        ];
    }

    public function getRegisteredUserByMonth(): array
    {
        $userByMonth = $this->userRepository->countUsersSubscriptionsByMonth();
        return $this->formatChartData($userByMonth);
    }

    public function getNumberOfLikeByMonth(): array
    {
        $likeByMonth = $this->likeRepository->countLikeByMonth();
        return $this->formatChartData($likeByMonth);
    }

    public function formatChartData(array $arrayData): array
    {
        $lastTwelveMonth = [];

        // Create array with month name and order them
        // Initialize to 0 for month without data
        $currentMonth = new DateTime('first day of this month');
        for ($i = 0; $i < 12; $i++) {
            $monthName = $currentMonth->format('M');
            $lastTwelveMonth[$monthName] = 0;
            $currentMonth->modify('-1 month');
        }

        $lastTwelveMonth = array_reverse($lastTwelveMonth);

        $keys = array_keys($arrayData[1]);
        $dataKey = $keys[1];

        // insert data foreach month
        foreach ($arrayData as $data) {
            $monthNumber = $data['month'];
            $monthName = (new DateTime())->setDate(date('Y'), $monthNumber, 1)->format('M');
            $lastTwelveMonth[$monthName] = $data[$dataKey];
        }

        return $lastTwelveMonth;
    }

    public function getMostLikedCategory(): array
    {
        $mostLikedCategories = $this->categoryRepository->getFiveMostLikedCategories();

        $categoriesWithLikes = [];
        foreach ($mostLikedCategories as $likedCategory) {
            $category = $likedCategory['category'];
            $numberOfLikes = (int) $likedCategory['numberOfLikes'];
            $categoriesWithLikes[$category] = $numberOfLikes;
        }
        return $categoriesWithLikes;
    }
}
