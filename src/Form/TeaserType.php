<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\File;

class TeaserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('video', FileType::class, [
                'label' => 'Votre vidéo :',
                'required' => true,
                'help' => 'file .mp4, .webm, .ogg, .wmv',
                'constraints' => [new File([
                    'mimeTypes' => ['video/mp4', 'video/webm', 'video/ogg', 'video/wmv']
                ])],
            ])
            ->add('secondStart', IntegerType::class, [
                'label' => 'Début :',
                'required' => false,
                'empty_data' => '0',
                'help' => 'What time start (in second)',
                'attr' => [
                    'placeholder' => '0',
                    'class' => "d-flex flex-column"
                ],

                'constraints' => [new PositiveOrZero()],
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Durée :',
                'required' => false,
                'empty_data' => '10',
                'help' => 'Duration teaser (in second)',
                'attr' => [
                    'placeholder' => '10',
                    'class' => "d-flex flex-column"
                ],
                'constraints' => [new GreaterThanOrEqual(
                    ['value' => 1]
                )],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
