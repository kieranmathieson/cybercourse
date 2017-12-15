<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminEditUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', IntegerType::class, [
                'disabled' => true,
                'label' => 'Id',
            ])
            ->add('username', TextType::class, [
                'label' => 'Username',
                'required' => true,
                'attr' => [
                    'help' => 'Unique username for this account.',
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Password',
                'attr' => [
                    'help' => 'New password, if you want to change it.',
                ],
            ])
            ->add('plainPasswordRepeat', PasswordType::class, [
                'label' => 'Repeat password',
                'mapped' => false,
                'attr' => [
                    'help' => 'New password, if you want to change it.',
                ],
            ])
            ->add('email', EmailType::class,  [
                'label' => 'Email',
                'attr' => [
                    'help' => 'Unique email for this account.',
                ],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'First name',
                'attr' => [
                    'help' => 'E.g., Jackie.',
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last name',
                'attr' => [
                    'help' => 'E.g., Robinson.',
                ],
            ])
            ->add('aboutMe', TextareaType::class, [
                'label' => 'About',
                'attr' => [
                    'help' => 'Whatever the user wants others to know.',
                ],
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'Enabled',
                'attr' => [
                    'help' => 'Is this account active?',
                ],
            ])
            ->add('shareDeetsWithClass', CheckboxType::class, [
                'label' => 'Share details',
                'attr' => [
                    'help' => 'If checked, other students in the same classes can see this user\'s name, about, and photos.',
                ],
            ])
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
