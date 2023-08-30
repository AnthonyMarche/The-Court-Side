<?php

namespace App\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

class NewsletterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => new TranslatableMessage('newsletter.title', [], 'admin'),
                'attr' => [
                    'placeholder' => new TranslatableMessage('newsletter.title-placeholder', [], 'admin'),
                ]
            ])
            ->add('content', CKEditorType::class, [
                'config' => [
                    'uiColor' => '#ededed',
                ],
                'attr' => [
                    'rows' => '12',
                ],
                'label' => new TranslatableMessage('newsletter.content', [], 'admin')
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
