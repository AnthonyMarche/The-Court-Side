<?php

namespace App\Form;

use App\Entity\User;
use App\Validator\Constraints\PasswordRequirements;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
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
                'mapped' => false
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class' => 'form-control mb-3 password-input'
                    ],
                ],
                'first_options' => [
                    'constraints' => new PasswordRequirements(),
                    'label' => new TranslatableMessage('usertype.new-password'),
                    'label_attr' => ['class' => 'text-white'],
                    'hash_property_path' => 'password'
                ],
                'second_options' => [
                    'label' => new TranslatableMessage('usertype.verify-password'),
                    'label_attr' => ['class' => 'text-white']
                ],
                'invalid_message' => 'The password fields must match.',
                'mapped' => false,
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
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
