<?php

namespace AnnonceBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContextInterface;

use UserBundle\Entity\Notification as Notif;

class Notification{

	private $securityContext;
	private $session;
	private $router;
	private $em;
	private $repository;
	private $me;

	public function __construct(EntityManager $entityManager, Session $session, SecurityContextInterface $securityContext, $router){

		$this->securityContext = $securityContext;
		$this->session = $session;
		$this->router = $router;
		$this->em = $entityManager;
		$this->repository = $this->em->getRepository("UserBundle:Notification");
		$this->me = $securityContext->getToken()->getUser();

	}


	public function get($notifId){
		$notif = $this->repository->find($notifId);
		if(!$notif || is_null($notif))
			throw new \Exception("Pas de notif");
		return $notif;
	}


	public function getNew($user){

		$notifs = $this->repository->findBy(array("user"=>$user, "supprimer"=>false), array("createdAt"=>"DESC"));
		if(!$notifs || is_null($notifs))
			throw new \Exception("Pas de notifs");
		return $notifs;
	}


	public function getNewUnread($user){

		$notifs = $this->repository->findBy(array("user"=>$user, "supprimer"=>false, "lue"=>false), array("createdAt"=>"DESC"));
		if(!$notifs || is_null($notifs))
			return [];
		return $notifs;
	}


	public function add($user, $annonce, $message, $type="Annonce", $reservation=null, $devis=null){
		$notif = new Notif();
		$notif->setAuteur($this->me)->setUser($user)->setTexte($message)->setType($type);
		
		if(!is_null($annonce))
			$notif->setAnnonce($annonce);

		if(!is_null($reservation))
			$notif->setReservation($reservation);

		if(!is_null($devis))
			$notif->setDevis($devis);


		$this->em->persist($notif);
		$this->em->flush();
		return $this;
	}

	public function supprimer($notifId){
		$notif = $this->repository->find($notifId);
		if(!$notif || is_null($notif))
			throw new \Exception("Pas de notif");

		$notif->setSupprimer(true);
		$this->em->persist($notif);
		$this->em->flush();
		return $this;
	}

	public function setLue($notifId){
		$notif = $this->repository->find($notifId);
		if(!$notif || is_null($notif))
			throw new \Exception("Pas de notif");
			
		$notif->setLue(true);
		$this->em->persist($notif);
		$this->em->flush();
		return $this;
	}
}


?>