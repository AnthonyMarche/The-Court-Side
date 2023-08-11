<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LanguageController extends AbstractController
{
    #[Route('/Language/{language}', name: 'app_language')]
    public function changeLanguage(string $language, Request $request): Response
    {
        $currentLocal = $request->getLocale();
        $currentUrl = $request->headers->get('referer');

        $newUrl = str_replace('/' . $currentLocal . '/', '/' . $language . '/', $currentUrl);
        $request->setLocale($language);

        return $this->redirect($newUrl);
    }
}
