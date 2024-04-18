<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add("username","hidden", array('required'=>false, "data"=>uniqid()))
        ->add('nom', 'text', array(
                'required'=>true, 
                'attr'=>array('placeholder'=>'Nom de famille')))
        ->add('prenom', 'text', array(
                'required'=>true, 
                'attr'=>array('placeholder'=>'Votre prénom')))
        ->add('anneeNaissance', 'choice', array(
                'choices'=>range(1940,2010),
                'required'=>true, 
                'choices_as_values' => true,
                'choice_label' => function ($choice) {
                    return $choice;
                },
                'attr'=>array('placeholder'=>'Année de naissance')))
        ->add('email', 'email', array(
                'required'=>true, 
                'attr'=>array('placeholder'=>'Adresse email')))
        ->add('conditionSite', 'checkbox', array(
                'required'=>true,
                "data"=>true
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }


    public function getName()
    {
        return $this->getBlockPrefix();
    }


}
