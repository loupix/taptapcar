<?php

namespace UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use UserBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface{

	public function load(ObjectManager $manager){
		$nbUser = 10;

		$_SERVER['REMOTE_ADDR'] = "127.0.0.1";
		$_SERVER['HTTP_USER_AGENT'] = "firefox cli";

		$noms = array("daniel","pierre","laurent","claude","claudel","petit");
		$prenoms = array("loic","pierre","paul","jacque","william","jeremy","anthony");
		for($i=0;$i<=$nbUser;$i++){
			$nom = $noms[array_rand($noms)];
			$prenom = $prenoms[array_rand($prenoms)];

			$user = new User();
			$user->setUsername(uniqid());
			$user->setNom($nom);
			$user->setPrenom($prenom);
			$user->setAnneeNaissance(1985);
			$user->setEmail('admin'.$i.'@gmail.com');
	        $user->setPlainPassword('admin');
	        $user->addRole("ROLE_ADMIN");
	        $user->setEnabled(true);
	        $user->setVerifie(true);
	        $user->setLastLogin(new \DateTime());

	        $this->addReference('user_'.$i, $user);

	        $manager->persist($user);
		}
		
        $manager->flush();

	}


	public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return 1;
    }
}
