<?php

namespace App\Controller;

use _PHPStan_5c71ab23c\Nette\Utils\DateTime;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Services\UserEditService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $user,
        UserRepository $userRepository,
        UserEditService $userEditService
    ): Response {
        $errors = [];

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        // lorsque l'utilisateur effectue sa modif, elle est enregistrée
        // il reste sur la même page (pas de redirect)
        // ToDo: mettre un flash message quand ils seront implémentés
        // ex. "Vos modifications ont bien été prises en compte"
        if ($form->isSubmitted() && $form->isValid()) {
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
                    $userRepository->save($user, true);
                }
            }
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'errors' => $errors,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
            // on récupère la session utilisateur et on l'invalide,
            // puis on supprime le token, avant de rafraîchir la page
            $request->getSession()->invalidate();
            $this->container->get('security.token_storage')->setToken(null);
        }


        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}
