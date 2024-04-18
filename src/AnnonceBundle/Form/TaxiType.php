<?php

namespace AnnonceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\DataTransformer\IntegerToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\FormType;

use libphonenumber\PhoneNumberFormat;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use AnnonceBundle\Form\DataTransformer\StringToPhoneNumberTransformer;

use GeographieBundle\Entity\Ville;


class TaxiType extends AbstractType
{   

    // The 'libphonenumber.phone_number_util' service
    private $phoneNumberUtil;

    public function __construct($util)
    {
        $this->phoneNumberUtil = $util;
    }

    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nom','text', array("required"=>true))
        
        ->add('telephone',PhoneNumberType::class, array(
                'default_region' => 'FR', 
                'format' => PhoneNumberFormat::NATIONAL,
                "required"=>true))
        
        ->add('immatriculation','text', array("required"=>true))

        ->add('heureDebut', 'time', array(
                'attr'=>array('placeholder'=>'hh:mm     -     08:30'),
                'required' => true, 
                'widget' => 'single_text',
                'input' => 'string',
                'invalid_message' => 'You entered an invalid value.',
                'with_seconds' => false
                ))
        ->add('heureFin', 'time', array(
                'attr'=>array('placeholder'=>'hh:mm     -     08:30'),
                'required' => true, 
                'widget' => 'single_text',
                'input' => 'string',
                'invalid_message' => 'You entered an invalid value.',
                'with_seconds' => false
                ))

        ->add('couleur', 'choice', array(
            "label" => "Couleur",
            "choices"=>array('Noir','Gris','Blanc','Jaune','Autre couleur'),
            'choices_as_values' => true,
            'choice_label' => function ($choice) {
                return $choice;
            },
            'required' => false,
            'expanded' => true,
            'multiple' => false
            ))
        ->add('wifi', 'choice',array(
            "choices"=>array('oui'=>true,'non'=>false),
            'choices_as_values' => true,
            'required' => false,
            'expanded' => true,
            'multiple' => false
            ))
        ->add('greencab', 'choice', array(
            "label" => "GreenCab",
            "choices"=>array('oui'=>true,'non'=>false),
            'choices_as_values' => true,
            'required' => false,
            'expanded' => true,
            'multiple' => false
            ))
        ->add('paiements', 'choice', array(
            "label" => "Paiements",
            "choices"=>array('Carte bleue','Espèce','Chèque'),
            'choices_as_values' => true,
            'choice_label' => function ($choice) {
                return $choice;
            },
            'required' => false,
            'expanded' => true,
            'multiple' => true
            ))
        ->add('facture', 'choice', array(
            "label" => "Facture",
            "choices"=>array('oui'=>true,'non'=>false),
            'choices_as_values' => true,
            'required' => false,
            'expanded' => true,
            'multiple' => false
            ))

        ->add('siegeBebe', 'choice', array(
            "label" => "Siège bébé",
            "choices"=>array('oui'=>true,'non'=>false),
            'choices_as_values' => true,
            'required' => false,
            'expanded' => true,
            'multiple' => false
            ))

        ->add('animaux', 'choice', array(
            "label" => "Animaux acceptés",
            "choices"=>array('oui'=>true,'non'=>false),
            'choices_as_values' => true,
            'required' => false,
            'expanded' => true,
            'multiple' => false
            ))

        ->add('secuSocial', 'choice', array(
            "label" => "Siège bébé",
            "choices"=>array('oui'=>true,'non'=>false),
            'choices_as_values' => true,
            'required' => false,
            'expanded' => true,
            'multiple' => false
            ))

        ->add('tarifJour', 'number', array(
            'attr'=>array('placeholder'=>'Prix en euros'),
            'label' => 'Tarif de jour',
            'precision' => 2,
            'required' => false, 
            'rounding_mode' => IntegerToLocalizedStringTransformer::ROUND_CEILING,
            'grouping' => \NumberFormatter::GROUPING_USED   ))

        ->add('tarifNuit', 'number', array(
            'attr'=>array('placeholder'=>'Prix en euros'),
            'label' => 'Tarif de nuit',
            'precision' => 2,
            'required' => false, 
            'rounding_mode' => IntegerToLocalizedStringTransformer::ROUND_CEILING,
            'grouping' => \NumberFormatter::GROUPING_USED   ))


        ->add('typeTarif', 'choice', array(
            "label" => "Couleur",
            "choices"=>array('Au compteur', 'Par kilomètres', 'Par heures', 'Par places','Prix unique'),
            'choices_as_values' => true,
            'choice_label' => function ($choice) {
                return $choice;
            },
            'required' => true
            ))
        
        ->add('nbPlaces','choice', array(
            'choices'=>range(1,6),
            'choices_as_values' => true,
            'choice_label' => function ($choice) {
                return $choice;
            },
            'label' => 'Nombres de places', 
            'required' => true,
            ))

        // ->add('forfaits')
        ->add('infosdivers', 'textarea', array(
            'attr'=>array('placeholder'=>'Informations complémentaires'),
            'max_length' => 500, 'required' => false, 
            'label' => 'Informations complémentaires', 
            'trim' => true, 'read_only' => false, 
            'empty_data'  => null,
            'error_bubbling' => false))

        ->add(
            $builder->create('ville', FormType::class, array('data_class'=>Ville::class, 'mapped'=>true))
                ->add('latitude', 'hidden')
                ->add('longitude', 'hidden')
                ->add('street_number', 'hidden')
                ->add('route', 'hidden')
                ->add('locality', 'hidden')
                ->add('administrative_area_level_1', 'hidden')
                ->add('country', 'hidden')
                ->add('postal_code', 'hidden')
            )
        ->add('save','submit', array("label"=>"Enregistrer"))
        ->add('modif','submit', array("label"=>"Modifier"))
        ->add('supprime','submit', array("label"=>"Supprimer"));


        $builder->get('telephone')->addModelTransformer(new StringToPhoneNumberTransformer($this->phoneNumberUtil));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AnnonceBundle\Entity\Taxi'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'annoncebundle_taxi';
    }


}
