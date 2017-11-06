<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseRegistrationFormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RegistrationFormType extends AbstractType
{
    /**
     * Use the existing FOS registration form as the parent.
     * @return string
     */
    public function getParent()
    {
        return BaseRegistrationFormType::class;
    }

    /**
     * Add fields to the parent form.
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Todo: add rest of fields.
        $builder
            ->add('firstName', TextType::class)
            ->add('lastName');
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getBlockPrefix()
    {
        return 'app_bundle_registration_form_type';
    }
}
