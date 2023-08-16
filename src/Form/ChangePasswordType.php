<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Translation\TranslatableMessage;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'label' => new TranslatableMessage('usertype.current-password'),
                'label_attr' => ['class' => 'text-white'],
                'attr' => [
                    'class' => 'form-control mb-3'
                ],
                'constraints' => new UserPassword(
                    [],
                    'mot de passe actuel incorrect'
                ),
            ])
            ->add('newPassword', ResetPasswordType::class, [
                'label' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => new TranslatableMessage('userprofile.save-btn'),
                'attr' => [
                    'class' => 'btn btn-outline-custom mx-md-2'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
