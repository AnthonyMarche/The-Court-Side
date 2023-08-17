<?php

namespace App\Form;

use App\Entity\User;
use App\Validator\Constraints\PasswordRequirements;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Validator\Constraints\IsTrue;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('newsletter', CheckboxType::class, [
                'required' => false,
                'label' => new TranslatableMessage('newsletter.register')
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => 'email@gmail.com'
                ],
                'label' => new TranslatableMessage('mail.register'),
            ])
            ->add('username', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => new TranslatableMessage('placeholder.register'),

                ],
                'label' => new TranslatableMessage('username.register'),

            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => new TranslatableMessage('messageagree.register'),
                    ]),
                ],
                'label' => new TranslatableMessage('agree.register'),

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
                    'label' => new TranslatableMessage('password.register'),
                    'label_attr' => ['class' => 'text-white'],
                    'hash_property_path' => 'password'
                ],
                'second_options' => [
                    'label' => new TranslatableMessage('usertype.verify-password'),
                    'label_attr' => ['class' => 'text-white']
                ],
                'invalid_message' => 'The password fields must match.',
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
