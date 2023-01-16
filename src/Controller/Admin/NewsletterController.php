<?php

namespace App\Controller\Admin;

use App\Services\Newsletter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsletterController extends AbstractController
{
    #[Route('/newsletter', name: 'app_newsletter')]
    public function index(Newsletter $newsletter): Response
    {
        $contactsStatus = $newsletter->checkContactsStatus('test@mail.com');

        return $this->render('newsletter/index.html.twig', [
            'contact_status' => $contactsStatus,
        ]);
    }
}
