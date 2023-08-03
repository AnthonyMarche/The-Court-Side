<?php

namespace App\Controller\Admin;

use App\Form\NewsletterType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

class NewsletterController extends AbstractController
{
    #[Route('/newsletter', name: 'app_newsletter')]
    public function index(Request $request, MailerInterface $mailer, UserRepository $userRepository): Response
    {
        // récupère tous les Users qui ont souscrit à la newsletter
        $users = $userRepository->findBy(['newsletter' => true]);

        $form = $this->createForm(NewsletterType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // récupère le contenu de la newsletter
            $newsletterTitle = $form->get('title')->getData();
            $newsletterContent = $form->get('content')->getData();
            // envoie un mail par User qui a souscrit
            foreach ($users as $user) {
                $userEmail = $user->getEmail();
                $email = (new Email())
                    ->from('do-not-reply@thecourtside.com')
                    ->to($userEmail)
                    ->subject($newsletterTitle)
                    ->html($this->renderView('admin/newsletter/NewsletterMailTemplate.html.twig', [
                        'newsletter_title' => $newsletterTitle,
                        'newsletter_content' => $newsletterContent,
                    ]));
                $mailer->send($email);
            }
            $this->addFlash(
                'success',
                new TranslatableMessage(
                    'newsletter.flash',
                    ['parameter' => 'value'],
                    'admin'
                )
            );
        }

        return $this->render('admin/newsletter/index.html.twig', [
            'newsletter_form' => $form->createView(),
            'nb_of_users_subs_to_newsletter' => $users,
        ]);
    }
}
