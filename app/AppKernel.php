<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new FOS\MessageBundle\FOSMessageBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            // new Nelmio\CorsBundle\NelmioCorsBundle(),
            // new FOS\RestBundle\FOSRestBundle(),
            new AppBundle\AppBundle(),
            new UserBundle\UserBundle(),
            new AnnonceBundle\AnnonceBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Lyssal\StructureBundle\LyssalStructureBundle(),
            new Lyssal\GeographieBundle\LyssalGeographieBundle(),
            new GeographieBundle\GeographieBundle(),
            new MessageBundle\MessageBundle(),
            new Maxmind\Bundle\GeoipBundle\MaxmindGeoipBundle(),
            // new Troopers\MangopayBundle\TroopersMangopayBundle(),
            // new FOS\FacebookBundle\FOSFacebookBundle(),
            // new Http\HttplugBundle\HttplugBundle(),
            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
            new Misd\PhoneNumberBundle\MisdPhoneNumberBundle(),
            new ApiBundle\ApiBundle(),
            new AdminBundle\AdminBundle(),
            new FourLabs\RobotsBundle\FourLabsRobotsBundle(),
            new Ensepar\Html2pdfBundle\EnseparHtml2pdfBundle(),

        );

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
            $bundles[] = new CoreSphere\ConsoleBundle\CoreSphereConsoleBundle();
            
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
