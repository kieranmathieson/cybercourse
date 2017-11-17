<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseProfileFormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProfileFormType extends AbstractType
{
    /**
     * Use the existing FOS profile form as the parent.
     * @return string
     */
    public function getParent()
    {
        return BaseProfileFormType::class;
    }

    /**
     * Add fields to the parent form.
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('aboutMe', TextareaType::class)
            ->add('shareDeetsWithClass');
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getBlockPrefix()
    {
        return 'app_bundle_profile_form_type';
    }
}
