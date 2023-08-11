<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Services\UserEditService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

#[Route('/profile', name:'app_user_')]
class UserController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;
    private UserRepository $userRepository;

    public function __construct(UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository)
    {
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $user,
        UserEditService $userEditService
    ): Response {
        $errors = [];

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        // lorsque l'utilisateur effectue sa modif, elle est enregistrée
        // il reste sur la même page (pas de redirect)
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUsername($form->get('username')->getData());
            $user->setNewsletter($form->get('newsletter')->getData());
            // mot de passe entré par l'utilisateur dans le formulaire
            $userInputPassword = $form->get('current_password')->getData();
            $userInputPassword = htmlspecialchars($userInputPassword);
            // nouveau mot de passe
            $newPassword = $form->get('new_password')->getData();
            $newPassword = htmlspecialchars($newPassword);
            // vérification du nouveau mot de passe
            $verifyPassword = $form->get('verify_password')->getData();
            $verifyPassword = htmlspecialchars($verifyPassword);
            // si l'utilisateur entre son mot de passe actuel pour en changer
            if ($userInputPassword) {
                $errors = $userEditService->checkEditFields(
                    $user,
                    $errors,
                    $userInputPassword,
                    $newPassword,
                    $verifyPassword
                );
                if (empty($errors)) {
                    // s'il n'y a pas d'erreurs, on hash le MdP avant de l'entrer en base
                    $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
                    // MàJ auto de "updated_at"
                    $date = new DateTime('now');
                    $user->setUpdatedAt($date);
                    $this->userRepository->save($user, true);
                    $this->addFlash('success', new TranslatableMessage('userprofile.edit-profile-flash'));
                    return $this->redirectToRoute('app_user_show', ['id' => $user->getId()]);
                }
            } else {
                // MàJ auto de "updated_at"
                $date = new DateTime('now');
                $user->setUpdatedAt($date);
                $this->userRepository->save($user, true);
                $this->addFlash('success', new TranslatableMessage('userprofile.edit-profile-flash'));
                return $this->redirectToRoute('app_user_show', ['id' => $user->getId()]);
            }
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'errors' => $errors,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $this->userRepository->remove($user, true);
            // on récupère la session utilisateur et on l'invalide,
            // puis on supprime le token, avant de rafraîchir la page
            $request->getSession()->invalidate();
            $this->container->get('security.token_storage')->setToken();
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}
