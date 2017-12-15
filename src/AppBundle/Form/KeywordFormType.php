<?php
/**
 * Form for editing and adding keywords. Does not include adding or removing content linked to the keyword.
 * That is done on the content form.
 */
namespace AppBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KeywordFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label'=>'Keyword',
                'required' => true,
                'attr' => [
                    'help' => 'The keyword. It can have spaces, e.g., If statements.',
                ],
            ])
            ->add('notes', TextType::class, [
                'label'=>'Notes',
                'required' => false,
                'attr' => [
                    'help' => 'Anything you want to say about the keyword.',
                ],
            ])
//            ->add('contentEntities')
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Keyword'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_keyword';
    }


}
