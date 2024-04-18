<?php

namespace AnnonceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\DataTransformer\IntegerToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\FormType;

use libphonenumber\PhoneNumberFormat;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use AnnonceBundle\Form\DataTransformer\StringToPhoneNumberTransformer;

use GeographieBundle\Entity\Ville;
use GeographieBundle\Entity\Place;



class DemenagementTypeTransporteur extends AbstractType
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
        $builder->add("nom_societe", "text")

        ->add("telephone_societe", PhoneNumberType::class, array(
                'default_region' => 'FR', 
                'format' => PhoneNumberFormat::NATIONAL,
                "required"=>true))

        ->add('transporteur', 'hidden')

        ->add('jours_aller', 'choice', array(
                'choices'=>array('lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche'),
                'label'=>false,
                'expanded' => true,
                'multiple' => true))


        ->add('flexibilite', 'choice', array(
            'choices'=>array('fixes'=>'horraires fixes', 'variables'=>'horraires variables'),
            'expanded' => true,
            'multiple' => false
            ))

        ->add('heureAller', 'time', array(
                'attr'=>array('placeholder'=>'hh:mm     -     08:30'),
                'required' => true, 
                'widget' => 'single_text',
                'input' => 'string',
                'invalid_message' => 'You entered an invalid value.',
                'with_seconds' => false
                ))
        ->add('heureRetour', 'time', array(
                'attr'=>array('placeholder'=>'hh:mm     -     08:30'),
                'required' => true, 
                'widget' => 'single_text',
                'input' => 'string',
                'invalid_message' => 'You entered an invalid value.',
                'with_seconds' => false
                ))


        ->add('utilitaire', 'choice', array(
            'choices'=>array('Camion avec hayon'=>'avec', 'Camion sans hayon'=>'sans'),
            'choices_as_values' => true,
            'expanded' => true,
            'multiple' => false,
            'required' => false
            ))

        ->add('volume', 'choice', array(
            'choices'=>array('Moins de 5m²'=>'0-5', 'Entre 5m² et 10m²'=>'5-10','Entre 10m² et 20m²'=>'10-20',
                    'Entre 20m² et 35m²'=>'20-35','Entre 50m² et 80m²'=>'50-80','Plus de 80m²'=>'80+'),
            'choices_as_values' => true,
            'expanded' => true,
            'multiple' => false,
            'required' => false
            ))

        ->add('hauteur', 'choice', array(
            'choices'=>array('Moins de 2m40'=>'-240', '2m40'=>'240','2m70'=>'270','3m20'=>'320','3m80'=>'380','Plus de 3m80'=>'+380'),
            'choices_as_values' => true,
            'expanded' => true,
            'multiple' => false,
            'required' => false
            ))

        ->add('largeur', 'choice', array(
            'choices'=>array('Moins de 1m90'=>'-190', '1m90'=>'190','2m20'=>'220','2m30'=>'230','2m50'=>'250','2m70'=>'270','Plus de 2m70'=>'+270'),
            'choices_as_values' => true,
            'expanded' => true,
            'multiple' => false,
            'required' => false
            ))

        ->add('longueur', 'choice', array(
            'choices'=>array('Moins de 5m90'=>'-590', '5m90'=>'590','7m00'=>'700','8m00'=>'800','10m00'=>'1000','18m50'=>'1850','Plus de 18m50'=>'+1850'),
            'choices_as_values' => true,
            'expanded' => true,
            'multiple' => false,
            'required' => false
            ))

        ->add('tarif', 'number', array(
            'attr'=>array('placeholder'=>'Prix en euros'),
            'label' => 'Prif en euros',
            'precision' => 2,
            'required' => false, 
            'rounding_mode' => IntegerToLocalizedStringTransformer::ROUND_CEILING,
            'grouping' => \NumberFormatter::GROUPING_USED))

        ->add('tarification', 'choice', array(
            "label" => "Paiements",
            "choices"=>array('Par kilomètres', 'De l\'heure','sur devis','Forfait','jour'),
            'choices_as_values' => true,
            'choice_label' => function ($choice) {
                return $choice;
            },
            'expanded' => false,
            'multiple' => false,
            'required' => true
            ))

        ->add('paiements', 'choice', array(
            "label" => "Paiements",
            "choices"=>array('Carte bleue','Espèce','Chèque', 'CESU (Chèque emploi service)'),
            'choices_as_values' => true,
            'choice_label' => function ($choice) {
                return $choice;
            },
            'expanded' => true,
            'multiple' => true
            ))


        ->add('infosdivers', 'textarea', array(
            'attr'=>array('placeholder'=>'Informations complémentaires'),
            'max_length' => 500, 'required' => false, 
            'label' => 'Informations complémentaires', 
            'trim' => true, 'read_only' => false, 
            'empty_data'  => null,
            'error_bubbling' => false))


        ->add(
            $builder->create('lieu', FormType::class, array('data_class'=>Place::class, 'mapped'=>true))
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

        
        ->add("save","submit", array("label"=>"Enregistrer"))
        ->add('modif','submit', array("label"=>"Modifier"))
        ->add('supprime','submit', array("label"=>"Supprimer"));


        $builder->get('telephone_societe')->addModelTransformer(new StringToPhoneNumberTransformer($this->phoneNumberUtil));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AnnonceBundle\Entity\Demenagement'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'annoncebundle_demenagement_transporteur';
    }


}
