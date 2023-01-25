<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutController extends AbstractController
{
    #[Route('/aboutpage', name: 'app_about')]
    public function aboutpageAction(): Response
    {

        return $this->render('/home/aboutpage.html.twig');
    }
}
