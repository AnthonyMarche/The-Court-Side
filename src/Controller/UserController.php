<?php

namespace App\Controller;

use _PHPStan_5c71ab23c\Nette\Utils\DateTime;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
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
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
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
                if (!$this->passwordHasher->isPasswordValid($user, $userInputPassword)) {
                    $errors[] = "Le mot de passe entré est incorrect";
                }
                if (!$newPassword || empty(trim($newPassword))) {
                    $errors[] = "Vous devez entrer un nouveau mot de passe";
                }
                if (!$verifyPassword || empty(trim($verifyPassword))) {
                    $errors[] = "Vous devez vérifier votre mot de passe";
                }
                if ($newPassword != $verifyPassword) {
                    $errors[] = "Le nouveau mot de passe et sa vérification doivent être identiques";
                }
                if ($userInputPassword == $newPassword) {
                    $errors[] = "Votre mot de passe actuel et le nouveau ne doivent pas être identiques";
                }
                if (empty($errors)) {
                    // s'il n'y a pas d'erreurs, on hash le MdP avant de l'entrer en base
                    $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
                    // sécurisation de l'email (pour éviter l'injection de code) : au cas où l'utilisateur
                    // va dans l'inspecteur pour regarder l'input email qui est caché, on entre en BDD le vrai mail
                    $user->setEmail($user->getEmail());
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
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
