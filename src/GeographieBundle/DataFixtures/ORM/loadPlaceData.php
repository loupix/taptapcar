<?php

namespace GeographieBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use GeographieBundle\Entity\Place;

use AnnonceBundle\Entity\Annonce;
use AnnonceBundle\Entity\Demenagement;

class LoadPlaceData extends AbstractFixture implements OrderedFixtureInterface{

	public function load(ObjectManager $manager){