<?php

namespace UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use GeographieBundle\Entity\Place;

use AnnonceBundle\Entity\Annonce;
use AnnonceBundle\Entity\Demenagement;

class LoadDemenagementData extends AbstractFixture implements OrderedFixtureInterface{

	public function load(ObjectManager $manager){
        $nbUser = 10;
        $nbVille = 99;
        $nbAnnonce = 10;

        // Transporteur
        for($i=0;$i<$nbAnnonce;$i++){


            $Demenagement = new Demenagement();
            $Demenagement->setTransporteur(true);
            $Demenagement->setTelephoneSociete("0387780916");
            $Demenagement->setNomSociete("Test Corp");

            // genere un lieu hasard a partir des ville
            $Place = new Place();
            $Ville = $this->getReference('ville_'.rand(0,$nbVille));

            // heure debut et fin
            $heureAller = new \DateTime();
            $heureRetour = new \DateTime();

            $heureAller->setTime(rand(6,10), rand(0,59));
            $heureRetour->setTime(rand(15,19), rand(0,59));

            $Demenagement->setHeureAller($heureAller);
            $Demenagement->setHeureRetour($heureRetour);

            $Demenagement->setFlexibilite()

            

            $Place->setGoogleId(uniqid());
            $Place->setAdresse("Rue du chemin du près");
            $Place->setLatitude($Ville->getLatitude());
            $Place->setLongitude($Ville->getLongitude());
            $Place->setVille($Ville);
            $manager->persist($Place);

            $Demenagement->setPlace($place);


            // creer l'annonce
            $Annonce = new Annonce();
            $Annonce->setAuteur($this->getReference('user_'.rand(0,$nbUser)));
            $Annonce->setDemenagement($Demenagement);
            $manager->persist($Annonce);

        }


	}


	public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return 3;
    }
}
?>