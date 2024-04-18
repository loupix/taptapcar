<?php

namespace AnnonceBundle\Service;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContextInterface;



class sendEmail{

	private $request;
	private $mailer;
	private $session;
	private $router;
	private $em;
	private $securityContext;

	protected $user;
	protected $me;
	protected $annonce;
	protected $reservation;
	protected $devis;

	private $emailAdmin;
	private $emailDevelopper;


	public function __construct($mailer, EngineInterface $templating, EntityManager $entityManager, Session $session, SecurityContextInterface $securityContext, $router, $arrayParams){
		$this->mailer = $mailer;
		$this->templating = $templating;
		$this->session = $session;
		$this->router = $router;
		$this->em = $entityManager;
		$this->securityContext = $securityContext;

		$this->me = $securityContext->getToken()->getUser();

		$this->emailAdmin = $arrayParams['Admin'];
		$this->emailDevelopper = $arrayParams['Developper'];
	}


	public function setRequest(Request $request){
		$this->request = $request;
		return $this;
	}


	public function setUser($user){
		$this->user = $user;
		return $this;
	}

	public function setAnnonce($annonce){
		$this->annonce = $annonce;
		return $this;
	}

	public function setReservation($reservation){
		$this->reservation = $reservation;
		$this->setAnnonce($reservation->getAnnonce());
		$this->setUser($reservation->getAuteur());
		return $this;
	}







	public function getAnnonce(){
		return $this->annonce;
	}

	public function getReservation(){
		return $this->reservation;
	}

	public function getDevis(){
		return $this->devis;
	}


	public function getUser(){
		return $this->user;
	}





	public function setDevis($devis){
		$this->devis = $devis;
		return $this;
	}

	public function sendMailReservation($reservation){
		$url = "";
		// $url = $this->generateUrl("annonce_annonce_get", array('annonceId'=>$reservation->getAnnonce()->getId()));
		try{
			$data = array("type"=>$reservation->getAnnonce()->getType(), "url"=>$url, "user"=>$this->getUser());
			return $this->sendMailUser("Nouveau paiement en ligne", "emailReservation.html.twig", $data);
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}


	private function sendMailUser($sujet, $template, $array){
		try{
			$array['sujet']=$sujet;
			$array['urlSite']="";
			$message = \Swift_Message::newInstance()
                ->setSubject($sujet)
                ->setFrom(['ne_pas_repondre@formidrive.com'=>"Formidrive"])
                ->setTo($this->getUser()->getEmail())
                ->setBody(
                    $this->templating->render($template, $array)
                    ,'text/html'
                );
            // print_r($this->mailer->send($message));
            // die();
            return $this->mailer->send($message);
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}


	private function sendMailAdmin($sujet, $template, $array){
		try{
			$array['sujet']=$sujet;
			$message = \Swift_Message::newInstance()
                ->setSubject($sujet)
                ->setTo($this->emailAdmin)
                ->setBody(
                    $this->templating->render($template, $array)
                    ,'text/html'
                );
            return $this->mailer->send($message);
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}

	private function sendMailDevelopper($sujet, $template, $array){
		try{
			$array['sujet']=$sujet;
			$message = \Swift_Message::newInstance()
                ->setSubject($sujet)
                ->setTo($this->emailDevelopper)
                ->setBody(
                    $this->templating->render($template, $array)
                    ,'text/html'
                );
            return $this->mailer->send($message);
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}





	public function annonceValidee(){
		$sujet = "Votre annonce a été validés.";
		$template = "emails/annonceValidee.html.twig";
		$url = $this->router->generate("annonce_annonce_get", array("annonceId"=>$this->getAnnonce()->getId()), true);
		$data = array(
			"url"=>$url,
			"user"=>$this->getUser(),
			"type"=>$this->getAnnonce()->getType()
		);
		try{
			return $this->sendMailUser($sujet, $template, $data);
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}





	public function annonceRefusee(){
		$sujet = "Votre annonce a été refusé.";
		$template = "emails/annonceREfusee.html.twig";
		$url = $this->router->generate("annonce_annonce_get", array("annonceId"=>$this->getAnnonce()->getId()), true);
		$data = array(
			"url"=>$url,
			"user"=>$this->getUser(),
			"type"=>$this->getAnnonce()->getType()
		);
		try{
			return $this->sendMailUser($sujet, $template, $data);
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}





	public function compteDesactivee(){
		$sujet = "Votre annonce a été désactivé.";
		$template = "emails/compteDesactivee.html.twig";
		$url = $this->router->generate("profile_accueil", array(), true);
		$data = array(
			"url"=>$url,
			"user"=>$this->getUser()
		);
		try{
			return $this->sendMailUser($sujet, $template, $data);
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}





	public function compteVerifie(){
		$sujet = "Votre annonce a été vérifié.";
		$template = "emails/compteVerifie.html.twig";
		$url = $this->router->generate("profile_accueil", array(), true);
		$data = array(
			"url"=>$url,
			"user"=>$this->getUser()
		);
		try{
			return $this->sendMailUser($sujet, $template, $data);
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}





	public function compteSupprime(){
		$sujet = "Votre annonce a été suprimer";
		$template = "emails/compteSupprime.html.twig";
		$url = $this->router->generate("profile_accueil", array(), true);
		$data = array(
			"url"=>$url,
			"user"=>$this->getUser()
		);
		try{
			return $this->sendMailUser($sujet, $template, $data);
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}












	///////// RESERVATION









	public function reservationNouveau(){
		$sujet = "Nouvelle réservation.";
		$template = "emails/reservationNouveau.html.twig";
		$url = $this->router->generate("annonce_annonce_get", array("annonceId"=>$this->getAnnonce()->getId()), true);
		$data = array(
			"url"=>$url,
			"user"=>$this->getAnnonce()->getAuteur(),
			"type"=>$this->getAnnonce()->getType()
		);
		try{
			return $this->sendMailUser($sujet, $template, $data);
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}





	public function reservationAnnuler(){
		$sujet = "Votre réservation a été annuler.";
		$template = "emails/reservationAnnuler.html.twig";
		$url = $this->router->generate("annonce_annonce_get", array("annonceId"=>$this->getAnnonce()->getId()), true);
		$data = array(
			"url"=>$url,
			"user"=>$this->getReservation()->getAuteur(),
			"type"=>$this->getAnnonce()->getType()
		);
		try{
			return $this->sendMailUser($sujet, $template, $data);
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}





	public function reservationValide(){
		$sujet = "Votre réservation a été validés.";
		$template = "emails/reservationValide.html.twig";
		$url = $this->router->generate("annonce_annonce_get", array("annonceId"=>$this->getAnnonce()->getId()), true);
		$data = array(
			"url"=>$url,
			"user"=>$this->getReservation()->getAuteur(),
			"type"=>$this->getAnnonce()->getType()
		);
		try{
			return $this->sendMailUser($sujet, $template, $data);
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}












	//////////////////////// DEVIS









	public function propositionDevis(){
		$sujet = "Votre annonce a reçu un devis";
		$template = "emails/propositionDevis.html.twig";
		$url = $this->router->generate("annonce_annonce_get", array("annonceId"=>$this->getAnnonce()->getId()), true);
		$data = array(
			"url"=>$url,
			"user"=>$this->getUser(),
			"type"=>$this->getAnnonce()->getType()
		);
		try{
			return $this->sendMailUser($sujet, $template, $data);
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}




	public function devisAccepter(){
		$sujet = "Votre devis a été validé.";
		$template = "emails/devisAccepter.html.twig";
		$url = $this->router->generate("annonce_annonce_get", array("annonceId"=>$this->getAnnonce()->getId()), true);
		$data = array(
			"url"=>$url,
			"user"=>$this->getUser(),
			"type"=>$this->getAnnonce()->getType()
		);
		try{
			return $this->sendMailUser($sujet, $template, $data);
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}




	public function devisRefusee(){
		$sujet = "Votre devis a été refusé.";
		$template = "emails/devisRefusee.html.twig";
		$url = $this->router->generate("annonce_annonce_get", array("annonceId"=>$this->getAnnonce()->getId()), true);
		$data = array(
			"url"=>$url,
			"user"=>$this->getUser(),
			"type"=>$this->getAnnonce()->getType()
		);
		try{
			return $this->sendMailUser($sujet, $template, $data);
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}










	/////////////////// PAIEMENTS





	public function paiementAccepter(){
		$sujet = "Votre annonce a été validés.";
		$template = "emails/paiementAccepter.html.twig";
		$url = $this->router->generate("annonce_annonce_get", array("annonceId"=>$this->getAnnonce()->getId()), true);
		$data = array(
			"url"=>$url,
			"user"=>$this->getUser(),
			"type"=>$this->getAnnonce()->getType()
		);
		try{
			return $this->sendMailUser($sujet, $template, $data);
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}




	public function paiementRefusee(){
		$sujet = "Votre annonce a été validés.";
		$template = "emails/paiementRefusee.html.twig";
		$url = $this->router->generate("annonce_annonce_get", array("annonceId"=>$this->getAnnonce()->getId()), true);
		$data = array(
			"url"=>$url,
			"user"=>$this->getUser(),
			"type"=>$this->getAnnonce()->getType()
		);
		try{
			return $this->sendMailUser($sujet, $template, $data);
		}catch(\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}
}