<?php

namespace App\Controller;

use App\Form\NewsletterType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewsletterController extends AbstractController
{
    #[Route('/newsletter', name: 'app_newsletter')]
    public function index(Request $request): Response
    {

        $form = $this->createForm(NewsletterType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $newsletter = $form->getData();
            dd($newsletter);
        }

        return $this->render('/newsletter/index.html.twig', [
            'newsletter_form' => $form->createView(),
        ]);
    }
}
