<?php

namespace App\Form;

use App\Validator\Constraints\PasswordRequirements;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('newPassword', RepeatedType::class, [
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
                    'label_attr' => ['class' => 'text-white']
                ],
                'second_options' => [
                    'label' => new TranslatableMessage('usertype.verify-password'),
                    'label_attr' => ['class' => 'text-white']
                ],
                'invalid_message' => 'The password fields must match.',
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
