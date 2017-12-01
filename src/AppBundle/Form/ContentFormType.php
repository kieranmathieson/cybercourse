<?php

namespace AppBundle\Form;

use AppBundle\Entity\Content;
use AppBundle\Helper\ContentTypes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Content $contentEntity */
//        $contentEntity = $builder->getData();
        $builder
            ->add('title');
        return;
        if ($contentEntity->getContentType() === ContentTypes::LESSON) {
            $builder->add(
                'shortMenuTreeTitle',
                TextType::class,
                [
                    'attr' => [
                        'help' => 'This is the title used in the lesson tree.',
                    ],
                ]
            );
        }
        //A checkbox that must be clicked to make the slug editable.
        $builder
            ->add(
            'changeSlug',
            CheckboxType::class, [
                'label' => 'Change slug?',
                'value' => '',
                'mapped' => false, //Not an entity field.
                'required' => false,
                'attr' => [
                    'help' => 'Check if you want to change the slug.',
                ],
            ])
            ->add('slug', TextType::class, ['disabled' => true])
            ->add(
            'summary',
            TextareaType::class,[
                'attr' => [
                    'help' => 'Short summary used in lists. One sentence is good.',
                    'title' => 'Short summary used in lists',
                ],
            ])
            ->add(
            'body',
            TextareaType::class, [
                'attr' => [
                    'help' => 'Main content.',
                ],
            ]);
        if ($contentEntity->getContentType() === ContentTypes::PATTERN) {
            $builder
                ->add(
                'patternCondition',
                TextareaType::class, [
                    'attr' => [
                        'help' => 'When the pattern is relevant.',
                    ],
                ])
                ->add(
                    'patternAction',
                    TextareaType::class, [
                    'attr' => [
                        'help' => 'What the pattern is.',
                    ],
                ]);
        }
        $builder
            ->add(
                'isAvailable',
                CheckboxType ::class, [
                    'label' => 'Available?',
                    'required' => false,
                    'attr' => [
                        'help' => 'If not checked, students will not see the content.',
                    ],
                ])
            ->add(
                'notes',
                TextareaType::class, [
                'attr' => [
                    'help' => 'Tasks, deleted content, whatevs.',
                ],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'AppBundle\Entity\Content',
            ]
        );
    }

//    public function getBlockPrefix()
//    {
//        return 'app_bundle_content_form_type';
//    }
}
