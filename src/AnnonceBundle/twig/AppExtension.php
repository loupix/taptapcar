<?php
namespace AnnonceBundle\Twig;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('price', array($this, 'priceFilter')),
            new \Twig_SimpleFilter('taille', array($this, 'tailleFilter')),
            new \Twig_SimpleFilter('volume', array($this, 'volumeFilter')),
        );
    }

    public function priceFilter($number, $decimals = 0, $decPoint = '.', $thousandsSep = ',')
    {
        $price = number_format($number, $decimals, $decPoint, $thousandsSep);
        $price = '€'.$price;

        return $price;
    }

    public function tailleFilter($number, $decimals = 2, $decPoint = '.', $thousandsSep = ',')
    {
        $taille = number_format($number/100, $decimals, $decPoint, $thousandsSep);
        $taille = str_replace(".", "m", $taille);
        $taille = str_replace("-", "Moins de ", $taille);
        $taille = str_replace("+", "Plus de ", $taille);

        return $taille;
    }

    public function volumeFilter($value)
    {
        $vols = split("-",$value);
        if(count($vols)>1)
            return "Entre ".$vols[0]." et ".$vols[1];
        else
            return "Plus de ".str_replace("+", "", $value);
    }

    public function getName()
    {
        return 'app_extension';
    }
}
?>