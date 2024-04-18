<?php

namespace AnnonceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\DataTransformer\IntegerToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\FormType;

use GeographieBundle\Entity\Ville;
use GeographieBundle\Entity\Place;


class DemenagementTypeRecherche extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('transporteur', 'hidden')

        ->add('jours_unique', 'date', array(
                'widget' => 'single_text',
                'required' => false, 
                'attr'=>array('placeholder'=>'jj/mm/yyyy : 07/08/2016'),
                'input'=>'string',
                'format'=>'d/M/y', 
                'empty_value' => array('year' => 'Année', 'month' => 'Mois', 'day' => 'Jour'),
                'pattern' => "{{ day }}/{{ month }}/{{ year }}"))


        ->add('heureUnique', 'time', array(
                'attr'=>array('placeholder'=>'hh:mm     -     08:30'),
                'required' => true, 
                'widget' => 'single_text',
                'input' => 'string',
                'invalid_message' => 'You entered an invalid value.',
                'with_seconds' => false
                ))



        ->add('budgetApproximatif', 'choice', array(
            "label" => "Budget approximatif",
            "choices"=>array('20 à 100 €'=>'20-100','100 à 500 €'=>'100-150','500 à 1000 €'=>'500-1000','1000 à 1500 €'=>'1000-1500',
                '1500 à 3000 €'=>'1500-3000','3000 à 5000 €'=>'3000-5000','Plus de 5000 €'=>'+5000'),
            'choices_as_values' => true,
            'required' => true
            ))

        ->add('infosdivers', 'textarea', array(
            'attr'=>array('placeholder'=>'Informations complémentaires'),
            'max_length' => 500, 'required' => false, 
            'label' => 'Informations complémentaires', 
            'trim' => true, 'read_only' => false, 
            'empty_data'  => null,
            'error_bubbling' => false))


        // ->add('urgent', 'checkbox', array(
        //     "label" => "Urgent",
        //     'required' => false
        //     ))

        
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
        ->add("save","submit", array("label"=>"Enregistrer"))
        ->add('modif','submit', array("label"=>"Modifier"))
        ->add('supprime','submit', array("label"=>"Supprimer"));
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
        return 'annoncebundle_demenagement_recherche';
    }


}
