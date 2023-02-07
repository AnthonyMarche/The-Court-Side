<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\File;

class TeaserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('video', FileType::class, [
                'label' => new TranslatableMessage(
                    'teaser.video',
                    ['parameter' => 'value'],
                    'admin'
                ),
                'required' => true,
                'help' => new TranslatableMessage(
                    'teaser.extension',
                    ['parameter' => 'value'],
                    'admin'
                ),
                'constraints' => [
                    new File([
                        'mimeTypes' => ['video/mp4', 'video/webm', 'video/ogg', 'video/wmv']
                    ]),
                    new NotBlank()
                ],
            ])
            ->add('secondStart', IntegerType::class, [
                'label' => new TranslatableMessage(
                    'teaser.start',
                    ['parameter' => 'value'],
                    'admin'
                ),
                'required' => false,
                'empty_data' => '0',
                'help' => new TranslatableMessage(
                    'teaser.start-help',
                    ['parameter' => 'value'],
                    'admin'
                ),
                'attr' => [
                    'placeholder' => '0',
                    'class' => "d-flex flex-column"
                ],

                'constraints' => [new PositiveOrZero()],
            ])
            ->add('duration', IntegerType::class, [
                'label' => new TranslatableMessage(
                    'teaser.duration',
                    ['parameter' => 'value'],
                    'admin'
                ),
                'required' => false,
                'empty_data' => '10',
                'help' => new TranslatableMessage(
                    'teaser.duration-help',
                    ['parameter' => 'value'],
                    'admin'
                ),
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
