<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminEditUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', IntegerType::class, ['disabled' => true])
            ->add('username')
            ->add('plainPassword', TextType::class)
            ->add('plainPasswordRepeat', TextType::class, ['mapped' => false])
            ->add('email')
            ->add('firstName')
            ->add('lastName')
            ->add('aboutMe')
            ->add('enabled')

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\User'
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_admin_edit_user';
    }
}
