<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'username'
            )
            ->add(
                'current_password',
                PasswordType::class,
                array(
                    'mapped' => false,
                    'required' => false,
                    'label' => new TranslatableMessage('usertype.username'))
            )
            ->add(
                'new_password',
                PasswordType::class,
                array(
                    'mapped' => false,
                    'required' => false,
                    'label' => new TranslatableMessage('usertype.current-password'))
            )
            ->add(
                'verify_password',
                PasswordType::class,
                array(
                    'mapped' => false,
                    'required' => false,
                    'label' => new TranslatableMessage('usertype.new-password'))
            )
            ->add(
                'newsletter',
                CheckboxType::class,
                array(
                    'required' => false,
                    'label' => new TranslatableMessage('usertype.confirm-password'))
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
