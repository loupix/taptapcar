<?php

namespace AnnonceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\DataTransformer\IntegerToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\FormType;

use GeographieBundle\Entity\Ville;
use GeographieBundle\Entity\Place;


class CovoiturageTypeRegulier extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('regulier','hidden')
        ->add('role', 'choice', array(
            "label" => "Votre rôle",
            "choices"=>array('Toujours conducteur','Passager et conducteur'),
            'expanded' => true,
            'multiple' => false
            ))

        ->add('jours_aller', 'choice', array(
                'choices'=>array('lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche'),
                'label'=>false,
                'expanded' => true,
                'multiple' => true))

        ->add('jours_retour', 'choice', array(
                'choices'=>array('lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche'),
                'label'=>false,
                'expanded' => true,
                'multiple' => true))

        ->add('heureAller', 'time', array(
                'widget' => 'single_text',
                'attr'=>array('placeholder'=>'hh:mm : 08:30'),
                'required' => false, 
                'input' => 'string',
                'with_seconds' => false))

        ->add('heureRetour', 'time', array(
                'widget' => 'single_text',
                'attr'=>array('placeholder'=>'hh:mm : 08:30'),
                'required' => false, 
                'input' => 'string',
                // 'format'=>'h:i', 
                'with_seconds' => false))

        ->add('bagages','choice',array(
            'choices'=>array('Petits sacs', 'Bagages à main','Valises','Aucun bagages autorisé'),
            'choices_as_values' => true,
            'choice_label' => function ($choice) {
                return $choice;
            },
            'expanded' => true,
            'multiple' => true,
            'required' => false
            ))
        ->add('flexibilite','choice',array(
            'choices'=>array('fixe'=>'horraires fixes', 'variable'=>'Horraires variables'),
            'expanded' => true,
            'multiple' => false,
            ))
        ->add('preferences', 'choice',array(
            'choices'=>array('Pause café', 'Pause dejeuner','Animaux autorisés','Musique','Cigarette','Détours','Je ne souhaite voyager qu\'avec des filles'),
            'choices_as_values' => true,
            'choice_label' => function ($choice) {
                return $choice;
            },
            'expanded' => true,
            'multiple' => true,
            'required' => false
            ))

        ->add('cout', 'number', array(
            'attr'=>array('placeholder'=>'Prix en euros'),
            'label' => 'Coût du trajet',
            'precision' => 2,
            'required' => true, 
            'rounding_mode' => IntegerToLocalizedStringTransformer::ROUND_CEILING,
            'grouping' => \NumberFormatter::GROUPING_USED))

        ->add('nbPlaces','choice', array(
            'choices'=>range(1,6),
            'choices_as_values' => true,
            'choice_label' => function ($choice) {
                return $choice;
            },
            'label' => 'Nombres de places', 
            'required' => true,
            ))

        ->add('repondSous', 'choice', array(
            "label" => "Je répond sous",
            "choices"=>array('A l\'heure indiquée','+/- 15 minutes','+/- 30 minutes','+/- 45 minutes','+/- 1 heure'),
            'choices_as_values' => true,
            'choice_label' => function ($choice) {
                return $choice;
            },
            'expanded' => false,
            'multiple' => false
            ))

        ->add('autoroute', 'checkbox', array(
            "label" => "Je prend l'autoroute",
            'required'=>false))

        ->add('infosdivers', 'textarea', array(
            'attr'=>array('placeholder'=>'Informations complémentaires'),
            'max_length' => 500, 'required' => false, 
            'label' => 'Informations complémentaires', 
            'trim' => true, 'read_only' => false, 
            'empty_data'  => null,
            'error_bubbling' => false))

        ->add(
            $builder->create('depart', FormType::class, array('data_class'=>Place::class, 'mapped'=>true))
                ->add('adresse', 'text', array('attr'=>array('placeholder'=>'Nom de ville ou code postal')))
                ->add('street_number', 'hidden')
                ->add('route', 'hidden')
                ->add('locality', 'hidden')
                ->add('administrative_area_level_1', 'hidden')
                ->add('country', 'hidden')
                ->add('postal_code', 'hidden')
                ->add('place_id', 'hidden')

                ->add('latitude', 'hidden')
                ->add('longitude', 'hidden')
            )
        ->add(
            $builder->create('rendezVous', FormType::class, array('data_class'=>Place::class, 'mapped'=>true))
                ->add('adresse', 'text', array('attr'=>array('placeholder'=>'Nom de ville ou code postal')))
                ->add('street_number', 'hidden')
                ->add('route', 'hidden')
                ->add('locality', 'hidden')
                ->add('administrative_area_level_1', 'hidden')
                ->add('country', 'hidden')
                ->add('postal_code', 'hidden')
                ->add('place_id', 'hidden')

                ->add('latitude', 'hidden')
                ->add('longitude', 'hidden')
            )
        ->add(
            $builder->create('arrivee', FormType::class, array('data_class'=>Place::class, 'mapped'=>true))
                ->add('adresse', 'text', array('attr'=>array('placeholder'=>'Nom de ville ou code postal')))
                ->add('street_number', 'hidden')
                ->add('route', 'hidden')
                ->add('locality', 'hidden')
                ->add('administrative_area_level_1', 'hidden')
                ->add('country', 'hidden')
                ->add('postal_code', 'hidden')
                ->add('place_id', 'hidden')

                ->add('latitude', 'hidden')
                ->add('longitude', 'hidden')
            )
        ->add(
            $builder->create('depot', FormType::class, array('data_class'=>Place::class, 'mapped'=>true))
                ->add('adresse', 'text', array('attr'=>array('placeholder'=>'Nom de ville ou code postal')))
                ->add('street_number', 'hidden')
                ->add('route', 'hidden')
                ->add('locality', 'hidden')
                ->add('administrative_area_level_1', 'hidden')
                ->add('country', 'hidden')
                ->add('postal_code', 'hidden')
                ->add('place_id', 'hidden')

                ->add('latitude', 'hidden')
                ->add('longitude', 'hidden')
            )
        ->add('save','submit', array("label"=>"Enregistrer"))
        ->add('modif','submit', array("label"=>"Modifier"))
        ->add('supprime','submit', array("label"=>"Supprimer"));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AnnonceBundle\Entity\covoiturage'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'annoncebundle_covoiturage_regulier';
    }


}
