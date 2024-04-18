<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

use GeographieBundle\Repository\PaysRepository;
use GeographieBundle\Repository\RegionRepository;
use GeographieBundle\Repository\VilleRepository;

use GeographieBundle\Entity\Ville;
use UserBundle\Entity\Vehicule;


class ProfileType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        // ->add("Nom","text")
        // ->add("Prenom","text")
        // ->add("Age","number")

        // ->add("pays",EntityType::class, array(
        //         'class'=>'GeographieBundle:Pays',
        //         'choice_label' => 'nom',
        //         'attr'=>array('placeholder'=>'Pays'),
        //         'expanded' => false,
        //         'multiple' => false))
        // ->add("region",EntityType::class, array(
        //         'class'=>'GeographieBundle:Region',
        //         'choice_label' => 'nom',
        //         'attr'=>array('placeholder'=>'Nom de la Région'),
        //         'expanded' => false,
        //         'multiple' => false))

        ->add(
            $builder->create('ville', FormType::class, array('data_class'=>Ville::class, 'mapped'=>true))
                ->add('nom', 'text', array('attr'=>array('placeholder'=>'Nom de ville ou code postal')))
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
            $builder->create('vehicule', FormType::class, array('data_class'=>Vehicule::class))
                ->add("type","choice",array(
                    "label"=>"Type de vehicule",
                    "choices"=>array('Citadine','Berline','Familiale','4x4','Camionette','Camion'),
                    'expanded' => false,
                    'multiple' => false
                    ))
                ->add("modele","text", array("label"=>"Modèle de voiture"))
                ->add("marque","text", array("label"=>"Marque du véhicule"))
                ->add("places","choice",array(
                    'label'=>'Nombres de places',
                    'choices'=>array('1','2','3','4','5','6'),
                    'required' => true,
                    'expanded' => false,
                    'multiple' => false))
                ->add("kilometrage",NumberType::class, array("label"=>"Kilométrage"))
            );
        
        // ->add("email","email", array("label"=>"Adresse email"))
        // ->add("telephone","number", array("label"=>"Numéro de téléphone"));
        // ->add("adresse","textarea", array("label"=>"Adresse"))
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\Profile'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'userbundle_profile';
    }


}
