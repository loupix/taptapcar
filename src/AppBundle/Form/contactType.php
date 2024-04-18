<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class contactType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email', array(
                'required'=>true, 
                'attr'=>array('placeholder'=>'Votre adresse email')))
        ->add('nom', 'text', array(
                'required'=>true, 
                'attr'=>array('placeholder'=>'Votre nom')))
        ->add('prenom', 'text', array(
                'required'=>true, 
                'attr'=>array('placeholder'=>'Votre prenom')))
        ->add('objet', 'text', array(
                'required'=>true, 
                'attr'=>array('placeholder'=>'Objet de votre demande')))
        ->add('message', 'textarea', array(
                'required'=>true, 
                'attr'=>array('placeholder'=>'Posez-votre question, ou parlez nous de votre problème')))
        ->add('save','submit')        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\contact'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_contact';
    }


}
