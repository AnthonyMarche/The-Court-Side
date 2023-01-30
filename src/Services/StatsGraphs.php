<?php

namespace App\Services;

use App\Repository\CategoryRepository;
use App\Repository\LikeRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class StatsGraphs
{
    private ChartBuilderInterface $chartBuilder;
    private UserRepository $userRepository;
    private CategoryRepository $categoryRepository;
    private LikeRepository $likeRepository;

    public function __construct(
        ChartBuilderInterface $chartBuilder,
        UserRepository $userRepository,
        CategoryRepository $categoryRepository,
        LikeRepository $likeRepository
    ) {
        $this->chartBuilder = $chartBuilder;
        $this->userRepository = $userRepository;
        $this->categoryRepository = $categoryRepository;
        $this->likeRepository = $likeRepository;
    }

    /**
     * Génère un graph.js avec le nombre d'utilisateurs inscrits mois par mois dans les douze derniers mois
     * @return Chart
     * @throws \Exception
     */
    public function viewSubscriptionsEvolution(): Chart
    {
        // récupération des douze derniers mois
        $lastTwelveMonths = [];
        for ($i = 1; $i <= 12; $i++) {
            $date = new DateTime($i . ' month ago');
            $date = $date->format('M');
            $lastTwelveMonths[] = $date;
        }
        // récupération des inscriptions utilisateurs mois par mois
        $usersMonthByMonth = $this->userRepository->getUsersSubscriptionsMonthByMonth();

        // récupération du nombre max, pour paramétrer le max de l'axe Y
        $maxAmountOfUsers = max($usersMonthByMonth);
        // on arrondit à la dizaine la plus proche
        $maxAmountOfUsers = ceil($maxAmountOfUsers / 10) * 10;

        // création du graphique
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        // remplissage avec les données
        $chart->setData([
            'labels' => $lastTwelveMonths,
            'datasets' => [
                [
                    'label' => 'Evolution de l\'inscription des visiteurs, par mois',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => $usersMonthByMonth,
                ],
            ],
        ]);
        // définition des options
        $chart->setOptions([
            'scales' => [
                'x' => [
                    'grid' => [
                        'color' => 'rgb(105, 105, 105)'
                    ],
                    'ticks' => [
                        'color' => 'rgb(150, 150, 150)'
                    ]
                ],
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => $maxAmountOfUsers,
                    'grid' => [
                        'color' => 'rgb(105, 105, 105)'
                    ],
                    'ticks' => [
                        'color' => 'rgb(150, 150, 150)'
                    ]
                ],
            ],
            'plugins' => [
                'legend' => [
                    'labels' => [
                        'color' => 'white',
                    ]
                ]
            ],
        ]);
        return $chart;
    }

    /**
     * Génère un graph.js avec le nombre de likes effectués sur les vidéos mois par mois dans les douze derniers mois
     * @return Chart
     * @throws \Exception
     */
    public function viewLikesEvolution(): Chart
    {
        // récupération des douze derniers mois
        $lastTwelveMonths = [];
        for ($i = 1; $i <= 12; $i++) {
            $date = new DateTime($i . ' month ago');
            $date = $date->format('M');
            $lastTwelveMonths[] = $date;
        }
        // récupération des inscriptions utilisateurs mois par mois
        $likesMonthByMonth = $this->likeRepository->getLikesMonthByMonth();

        // récupération du nombre max, pour paramétrer le max de l'axe Y
        $maxAmountOfLikes = max($likesMonthByMonth);
        // on arrondit à la dizaine la plus proche
        $maxAmountOfLikes = ceil($maxAmountOfLikes / 10) * 10;

        // création du graphique
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        // remplissage avec les données
        $chart->setData([
            'labels' => $lastTwelveMonths,
            'datasets' => [
                [
                    'label' => 'Evolution des likes, par mois',
                    'backgroundColor' => 'rgb(45, 191, 178)',
                    'borderColor' => 'rgb(45, 191, 178)',
                    'data' => $likesMonthByMonth,
                ],
            ],
        ]);
        // définition des options
        $chart->setOptions([
            'scales' => [
                'x' => [
                    'grid' => [
                        'color' => 'rgb(105, 105, 105)'
                    ],
                    'ticks' => [
                        'color' => 'rgb(150, 150, 150)'
                    ]
                ],
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => $maxAmountOfLikes,
                    'grid' => [
                        'color' => 'rgb(105, 105, 105)'
                    ],
                    'ticks' => [
                        'color' => 'rgb(150, 150, 150)'
                    ]
                ],
            ],
            'plugins' => [
                'legend' => [
                    'labels' => [
                        'color' => 'white',
                    ]
                ]
            ],
        ]);
        return $chart;
    }

    /**
     * Génère un graph.js qui montre le top cinq des catégories les plus likées
     * @return Chart
     */
    public function viewMostLikedCategories()
    {
        $categories = [];
        $likes = [];
        $mostLikedCategories = $this->categoryRepository->getMostLikedCategories();

        // on extrait les données "category" et "nombre de likes"
        for ($i = 0; $i < count($mostLikedCategories); $i++) {
            $categories[] = $mostLikedCategories[$i]['name'];
            $likes[] = $mostLikedCategories[$i]['totalLikes'];
        }

        // création du graphique
        $chart = $this->chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        // remplissage avec les données
        $chart->setData([
            'labels' => [
                $categories[0],
                $categories[1],
                $categories[2],
                $categories[3],
                $categories[4],
            ],
            'datasets' => [
                [
                    'label' => 'Top 5 des catégories les plus likées',
                    'backgroundColor' => [
                        'red',
                        'orange',
                        'blue',
                        'yellow',
                        'green'
                    ],
                    'borderColor' => 'transparent',
                    'data' => $likes,
                    'hoverOffset' => 4
                ],
            ],
        ]);
        $chart->setOptions([
            'plugins' => [
                'legend' => [
                    'labels' => [
                        'color' => 'white',
                    ]
                ]
            ],
        ]);

        return $chart;
    }
}
