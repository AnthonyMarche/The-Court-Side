<?php

namespace App\Services;

use _PHPStan_5c71ab23c\Nette\Utils\DateTime;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserEditService
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function checkEditFields(
        User $user,
        array $errors,
        string $userInputPassword,
        string $newPassword,
        string $verifyPassword
    ): array {
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
        return $errors;
    }
}
