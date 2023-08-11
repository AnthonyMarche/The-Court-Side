<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(name: 'app_')]
class LegalInfoController extends AbstractController
{
    #[Route('/legal-notice', name: 'legal_notice')]
    public function legalNotice(): Response
    {
        return $this->render('RGPD/legal_notice.html.twig');
    }

    #[Route('/privacy-policy', name: 'privacy_policy')]
    public function privacyPolicy(): Response
    {
        return $this->render('RGPD/privacy-policy.html.twig');
    }
}
