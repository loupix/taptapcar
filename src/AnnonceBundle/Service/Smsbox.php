<?php

namespace AnnonceBundle\Service;

use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberType;

use Symfony\Component\Security\Core\SecurityContextInterface;

class Smsbox{

	private $apiKey;
	private $securityContext;
	private $me;

	public function __construct(SecurityContextInterface $securityContext, $arrayParams){
		$this->apiKey = $arrayParams['key'];
		$this->securityContext = $securityContext;
		$this->me = $securityContext->getToken()->getUser();
	}


	private function get($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded', 'Content-Encoding: ISO-8859-15'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);

		$response = curl_exec($ch);
		return $response;
	}


	public function send($numero, $message){
		try{
			$phoneNumberUtil = PhoneNumberUtil::getInstance();

			if($phoneNumberUtil->getNumberType($phoneNumberUtil->parse($numero)) != PhoneNumberType::MOBILE)
				return;

			$url = "http://api.smsbox.fr/api.php?apikey=".rawurlencode($this->apiKey);
			$url .= "&msg=".rawurlencode($message);
			$url .= "&dest=".rawurlencode($numero);
			$url .= "&mode=Standard";
			$url .= "&strategy=2";
			return $this->get($url);
		}catch(\Exception $e){
			return;
		}
	}


	public function getCredit(){
		$url = "http://api.smsbox.fr/api.php?apikey=".rawurlencode($this->apiKey);
		$url .="&action=credit";
		return $this->get($url);
	}


	public function getError($errorCode){
		switch ($errorCode) {
			case 'ERROR 01':
				throw new Exception("Des paramètres sont manquants.", 1);
				break;

			case 'ERROR 02':
				throw new Exception("Identifiants incorrects, compte banni ou restriction par adresse IP.", 1);
				break;

			case 'ERROR 03':
				throw new Exception("Crédit épuisé ou insuffisant.", 1);
				break;

			case 'ERROR 04':
				throw new Exception("Numéro de destination invalide ou mal formaté.", 1);
				break;

			case 'ERROR 05':
				throw new Exception("Erreur d'éxécution interne à notre application.", 1);
				break;

			case 'ERROR':
				throw new Exception("L'envoi a échoué pour une autre raison.", 1);
				break;
			
			default:
				throw new Exception("Erreur inconnue.", 1);
				break;
		}
	}


	public function sendCovoiturage($Covoiturage, $reservation){
		return;
		if(!is_null($Covoiturage->getAnnonce()->getAuteur()->getTelephone())){
			try{
				$auteur = $reservation->getAuteur();
				$message = "Nouvelle réservation de covoiturage \r\n";
				$message .= "Départ: ".$Covoiturage->getDepart()->getAdresse()." \r\n";
				$message .= "Rendez-vous: ".$Covoiturage->getRendezVous()->getAdresse()." \r\n";
				$message .= "Arrivée: ".$Covoiturage->getArrivee()->getAdresse()." \r\n";
				$message .= "Dépot: ".$Covoiturage->getDepot()->getAdresse()." \r\n";
				$message .= "\r\n";
				$message .= "De ".$auteur->getNom()." ".$auteur->getPrenom();
				if($auteur->getVerifie())
					$message .= " (Utilisateur vérifié)";
				else
					$message .= " (Utilisateur non vérifié)";
				$message .= "\r\n";
				if($Covoiturage->getRegulier()){
					$message .= "le ".$reservation->getDate()->format("D d F")." à ".$reservation->getDate()->format("H:i")."\r\n";
				}

				$message .= $reservation->getNbPlaces()." place(s) \r\n";
				$message .= "Montant total : ".$reservation->getPrix()." €";

				return $this->send($Covoiturage->getAnnonce()->getAuteur()->getTelephone(), $message);
			}catch(\Exception $e){
				throw new Exception($e->getMessage());
			}
			
		}else{
			return false;
		}

	}



	public function sendDemenagement($Demenagement, $reservation){
		if(!is_null($Demenagement->getTelephoneSociete())){
			try{
				$auteur = $reservation->getAuteur();
				if($Demenagement->getTransporteur()){
					$message = "Nouvelle réservation de déménagement \r\n";
					$message .= "Départ: ".$Demenagement->getDepart()." \r\n";
					$message .= "Rendez-vous: ".$Demenagement->getRendezVous()." \r\n";
					$message .= "Arrivée: ".$Demenagement->getArrivee()." \r\n";
					$message .= "Dépot: ".$Demenagement->getDepot()." \r\n";
					$message .= "\r\n";
					$message .= "De ".$auteur->getNom()." ".$auteur->getPrenom();
					if($auteur->getVerifie())
						$message .= " (Utilisateur vérifié)";
					else
						$message .= " (Utilisateur non vérifié)";
					$message .= "\r\n";
					if($Demenagement->getTransporteur()){
						$message .= "le ".$reservation->getDate()->format("D d F")." à ".$reservation->getDate()->format("H:i")."\r\n";
					}

					$message .= $reservation->getNbPlaces()." place(s) \r\n";
					$message .= "Montant total : ".$reservation->getPrix()." €";
				}else{
					$message = "Nouveaux devis de déménagement \r\n";
					$message .= "Départ: ".$Demenagement->getDepart()->getAdresse()." \r\n";
					$message .= "Rendez-vous: ".$Demenagement->getRendezVous()->getAdresse()." \r\n";
					$message .= "Arrivée: ".$Demenagement->getArrivee()->getAdresse()." \r\n";
					$message .= "Dépot: ".$Demenagement->getDepot()->getAdresse()." \r\n";
					$message .= "\r\n";
					$message .= "De ".$auteur->getNom()." ".$auteur->getPrenom();
					if($auteur->getVerifie())
						$message .= " (Utilisateur vérifié)";
					else
						$message .= " (Utilisateur non vérifié)";
					$message .= "\r\n";
					if($Demenagement->getTransporteur()){
						$message .= "le ".$reservation->getDate()->format("D d F")." à ".$reservation->getDate()->format("H:i")."\r\n";
					}

					$message .= $reservation->getNbPlaces()." place(s) \r\n";
					$message .= "Montant total : ".$reservation->getPrix()." €";
				}

				return $this->send($Demenagement->getTelephoneSociete(), $message);
			}catch(\Exception $e){
				throw new \Exception($e);
			}
			
		}else{
			return false;
		}

	}


	public function sendTaxi($Taxi, $reservation){
		if(!is_null($Taxi->getTelephone())){
			try{
				$auteur = $reservation->getAuteur();
				$message = "Nouvelle réservation de taxi \r\n";
				$message .= "Départ: ".$reservation->getDepart()->getAdresse()." \r\n";
				$message .= "Arrivée: ".$reservation->getArrivee()->getAdresse()." \r\n";
				$message .= "\r\n";
				$message .= "De ".$auteur->getNom()." ".$auteur->getPrenom();
				if($auteur->getVerifie())
					$message .= " (Utilisateur vérifié)";
				else
					$message .= " (Utilisateur non vérifié)";
				$message .= "\r\n";
				$message .= $reservation->getNbPlaces()." place(s) \r\n";
				$message .= "Montant total : ".$reservation->getPrix()." €";

				return $this->send($Taxi->getTelephone(), $message);
			}catch(\Exception $e){
				throw new Exception($e->getMessage());
			}
			
		}else{
			return false;
		}
	}



	public function sendVtc($Vtc, $reservation){
		if(!is_null($Vtc->getTelephoneSociete())){
			try{
				$auteur = $reservation->getAuteur();
				$message = "Nouvelle réservation de vtc \r\n";
				$message .= "Départ: ".$reservation->getDepart()->getAdresse()." \r\n";
				$message .= "Arrivée: ".$reservation->getArrivee()->getAdresse()." \r\n";
				$message .= "\r\n";
				$message .= "De ".$auteur->getNom()." ".$auteur->getPrenom();
				if($auteur->getVerifie())
					$message .= " (Utilisateur vérifié)";
				else
					$message .= " (Utilisateur non vérifié)";
				$message .= "\r\n";
				$message .= $reservation->getNbPlaces()." place(s) \r\n";
				$message .= "Montant total : ".$reservation->getPrix()." €";

				return $this->send($Vtc->getTelephoneSociete(), $message);
			}catch(\Exception $e){
				throw new Exception($e->getMessage());
			}
			
		}else{
			return false;
		}
	}




	public function sendReservation($reservation){
		$annonce = $reservation->getAnnonce();
		if(!is_null($annonce->getAuteur()->getTelephone())){
			try{
				$auteur = $reservation->getAuteur();
				$message = "Réservation ".($reservation->getValider() ? "valider" : "annuler"). "\r\n";
				$message .= "\r\n";
				$message .= "De ".$auteur->getNom()." ".$auteur->getPrenom();
				if($annonce->getAuteur()->getVerifie())
					$message .= " (Utilisateur vérifié)";
				else
					$message .= " (Utilisateur non vérifié)";
				$message .= "\r\n";
				$message .= $reservation->getNbPlaces()." place(s) \r\n";
				$message .= "Montant total : ".$reservation->getPrix()." €";

				return $this->send($auteur->getProfile()->getTelephone(), $message);
			}catch(\Exception $e){
				throw new Exception($e->getMessage());
			}
			
		}else{
			return false;
		}
	}




	public function sendDevis($devis){
		$annonce = $devis->getAnnonce();
		if(!is_null($annonce->getAuteur()->getTelephone())){
			try{
				$auteur = $devis->getAuteur();
				$message = "Devis ".($devis->getValider() ? "valider" : "annuler"). "\r\n";
				$message .= "\r\n";
				$message .= "De ".$annonce->getAuteur()->getNom()." ".$annonce->getAuteur()->getPrenom();
				if($annonce->getAuteur()->getVerifie())
					$message .= " (Utilisateur vérifié)";
				else
					$message .= " (Utilisateur non vérifié)";
				$message .= "\r\n";
				$message .= $devis->getNbPlaces()." place(s) \r\n";
				$message .= "Montant total : ".$devis->getPrix()." €";

				return $this->send($auteur->getProfile()->getTelephone(), $message);
			}catch(\Exception $e){
				throw new Exception($e->getMessage());
			}
			
		}else{
			return false;
		}
	}
}