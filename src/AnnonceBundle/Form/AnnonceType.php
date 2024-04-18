<?php

namespace AnnonceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use GeographieBundle\Entity\Place;
use GeographieBundle\Entity\Ville;



class AnnonceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("categorie","choice", array(
            "choices"=>array('demenagement','vtc','taxi','covoiturage'),
            'expanded' => true,
            'multiple' => false,
            'required'=>true
            ))

        ->add(
            $builder->create('rayon', FormType::class)
                ->add("radius","text", array('required'=>false, 'attr'=>array('placeholder'=>'Rayon de recherche (km)')))
                ->add('latitude', 'hidden')
                ->add('longitude', 'hidden')
                ->add('street_number', 'hidden')
                ->add('route', 'hidden')
                ->add('locality', 'hidden')
                ->add('administrative_area_level_1', 'hidden')
                ->add('country', 'hidden')
                ->add('postal_code', 'hidden')
            )

        ->add(
            $builder->create('place', FormType::class)
                ->add(
                    $builder->create('from', FormType::class, array('data_class'=>Ville::class, 'mapped'=>true))
                    ->add("nom", "text", array('required'=>false, 'attr'=>array('placeholder'=>'De')))
                    ->add('latitude', 'hidden')
                    ->add('longitude', 'hidden')
                    ->add('street_number', 'hidden')
                    ->add('route', 'hidden')
                    ->add('locality', 'hidden')
                    ->add('administrative_area_level_1', 'hidden')
                    ->add('country', 'hidden')
                    ->add('postal_code', 'hidden')
                    )
                ->add(
                    $builder->create('to', FormType::class, array('data_class'=>Ville::class, 'mapped'=>true))
                    ->add("nom", "text", array('required'=>false, 'attr'=>array('placeholder'=>'À')))
                    ->add('latitude', 'hidden')
                    ->add('longitude', 'hidden')
                    ->add('street_number', 'hidden')
                    ->add('route', 'hidden')
                    ->add('locality', 'hidden')
                    ->add('administrative_area_level_1', 'hidden')
                    ->add('country', 'hidden')
                    ->add('postal_code', 'hidden')
                    )
                ->add("date", "text")
            )
        ->add("search","submit", array('label'=>'Rechercher'));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AnnonceBundle\Entity\Annonce'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'annoncebundle_annonce';
    }


}
