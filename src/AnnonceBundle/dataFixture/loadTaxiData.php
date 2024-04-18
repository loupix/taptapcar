<?php
// src/AppBundle/DataFixtures/ORM/LoadUserData.php

namespace AnnonceBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use UserBundle\Entity\User;

class LoadTaxiData implements FixtureInterface, ContainerAwareInterface
{


	/**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }



    public function load(ObjectManager $manager)
    {
        $userAdmin = new User();
        $userAdmin->setUsername('58b9ff5000167');
        $userAdmin->setPassword('lolo');

        

        $manager->persist($userAdmin);
        $manager->flush();
    }
}

?>