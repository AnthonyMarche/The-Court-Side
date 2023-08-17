<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\ProfileType;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/profile', name:'app_user_')]
class UserController extends AbstractController
{
    private UserRepository $userRepository;
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator, UserRepository $userRepository)
    {
        $this->translator = $translator;
        $this->userRepository = $userRepository;
    }

    #[Route('/{id}', name: 'show', methods: 'GET')]
    public function show(User $user): Response
    {
        /** @var User|null $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser->getId() !== $user->getId()) {
            throw new AccessDeniedException();
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit-profile', name: 'edit_profile', methods: ['GET', 'POST'])]
    public function editProfile(User $user, Request $request): Response
    {
        /** @var User|null $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser->getId() !== $user->getId()) {
            throw new AccessDeniedException();
        }

        $profileForm = $this->createForm(ProfileType::class, $user);
        $profileForm->handleRequest($request);

        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $user->setUsername($profileForm->get('username')->getData());
            $user->setNewsletter($profileForm->get('newsletter')->getData());
            $user->setUpdatedAt(new DateTime());

            $this->userRepository->save($user, true);

            $this->addFlash('success', $this->translator->trans('userprofile.edit-profile-flash'));
            return $this->redirectToRoute('app_user_show', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('user/edit_profile.html.twig', [
            'profileForm' => $profileForm,
            'user' => $user
        ]);
    }

    #[Route('/{id}/edit-password', name: 'edit_password', methods: ['GET', 'POST'])]
    public function editPassword(User $user, Request $request): Response
    {
        /** @var User|null $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser->getId() !== $user->getId()) {
            throw new AccessDeniedException();
        }

        $passwordForm = $this->createForm(ChangePasswordType::class, $user);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $userUpdate = $passwordForm->getData();

            $this->userRepository->save($userUpdate, true);

            $this->addFlash('success', 'Password change with success');
            return $this->redirectToRoute('app_user_show', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('user/edit_password.html.twig', [
            'passwordForm' => $passwordForm,
            'user' => $user
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $this->userRepository->remove($user, true);
            $request->getSession()->invalidate();
            $this->container->get('security.token_storage')->setToken();
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}
