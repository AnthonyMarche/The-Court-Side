<?php

namespace App\Services;

use App\Repository\UserRepository;
use DateTime;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class StatsGraphs
{
    private ChartBuilderInterface $chartBuilder;
    private UserRepository $userRepository;
    public function __construct(ChartBuilderInterface $chartBuilder, UserRepository $userRepository)
    {
        $this->chartBuilder = $chartBuilder;
        $this->userRepository = $userRepository;
    }

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
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => $maxAmountOfUsers,
                ],
            ],
        ]);
        return $chart;
    }
}
