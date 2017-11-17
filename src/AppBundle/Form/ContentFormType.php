<?php

namespace AppBundle\Form;

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
        $builder
            ->add('title', TextType::class)
            //A checkbox that must be clicked to make the slug editable.
            ->add('changeSlug', CheckboxType::class, ['mapped'=>false, 'required'=>false, ])
            ->add('slug', TextType::class, ['disabled' => true])
            ->add('summary', TextareaType::class)
            ->add('body', TextareaType::class)
            ->add('isAvailable', CheckboxType ::class, [
                'label' => 'Available?', 'required'=> false,
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Content'
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_content_form_type';
    }
}
