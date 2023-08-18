<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Compound;
use Symfony\Component\Validator\Constraints as Assert;

class PasswordRequirements extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\NotBlank(),
            new Assert\Type('string'),
            new Assert\Length([
                'min' => 12,
                'minMessage' => 'Votre mot passe doit contenir au moins 12 caractères'
                ]),
            new Assert\Regex([
                'pattern' => '/[A-Z]/',
                'message' => 'Votre mot passe doit contenir au moins une majuscule',
            ]),
            new Assert\Regex([
                'pattern' => '/[a-z]/',
                'message' => 'Votre mot passe doit contenir au moins une minuscule',
            ]),
            new Assert\Regex([
                'pattern' => '/\d/',
                'message' => 'Votre mot passe doit contenir au moins un chiffre',
            ]),
            new Assert\Regex([
                'pattern' => '/[!?@#$%^&*()+-]/',
                'message' => 'Votre mot passe doit contenir au moins un caractère spécial parmi !?@#$%^&*()+-',
            ]),
        ];
    }
}
