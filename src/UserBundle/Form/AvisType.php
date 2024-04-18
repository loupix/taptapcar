<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvisType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('note', 'choice', array(
            "choices"=>array(0,1,2,3,4,5),
            'required' => true,
            'expanded' => true,
            'multiple' => false
            ))
        ->add('message', 'textarea', array(
            'attr'=>array('placeholder'=>'Votre message ici'),
            'max_length' => 500, 'required' => false, 
            'label' => 'Informations complémentaires', 
            'trim' => true, 'read_only' => false, 
            'empty_data'  => null,
            'error_bubbling' => false));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\Avis'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'userbundle_avis';
    }


}
