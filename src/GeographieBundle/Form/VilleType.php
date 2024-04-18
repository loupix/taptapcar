<?php

namespace GeographieBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VilleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('street_number')->add('route')->add('locality')
        ->add('administrative_area_level_1')->add('country')
        ->add('postal_code')->add('nom')->add('slug')->add('codePostal')
        ->add('codeCommune')->add('latitude')->add('longitude')->add('description')
        ->add('siteWeb')->add('gentile')->add('departement');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GeographieBundle\Entity\Ville'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'geographiebundle_ville';
    }


}
