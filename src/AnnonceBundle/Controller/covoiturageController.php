<?php

namespace AnnonceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use AnnonceBundle\Entity\Annonce;
use AnnonceBundle\Entity\covoiturage;
use GeographieBundle\Entity\Ville;
use GeographieBundle\Entity\Place;
use UserBundle\Entity\User;
use UserBundle\Entity\Reservation;
use UserBundle\Entity\Paiement;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class covoiturageController extends Controller
{
    /**
     * @Route("/covoiturage/{id}", requirements={"id" = "\d+"}, options={"expose"=true})
     */
    public function getAction($id, Request $request)
    {   

        $id = $request->get('id');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AnnonceBundle:Covoiturage');
        $Covoiturage = $repository->find($id);
        if(!$Covoiturage || is_null($Covoiturage))
            throw new NotFoundHttpException("Pas d'annonce trouvés.");
            

        return $this->render('AnnonceBundle:covoiturage:get.html.twig', array(
            'covoiturage'=>$Covoiturage
        ));
    }












    /**
     * @Route("/covoiturage/ajout")
     */
    public function ajoutAction(Request $request)
    {
        $covRegulier = new covoiturage();
        $covRegulier->setRegulier(True);

        $covUnique = new covoiturage();
        $covUnique->setRegulier(False);

        $formRegulier = $this->createForm('AnnonceBundle\Form\CovoiturageTypeRegulier', $covRegulier, array(
            'action' => $this->generateUrl('annonce_covoiturage_ajout')));
        $formUnique = $this->createForm('AnnonceBundle\Form\CovoiturageTypeUnique', $covUnique, array(
            'action' => $this->generateUrl('annonce_covoiturage_ajout')));


        $formRegulier->handleRequest($request);
        $formUnique->handleRequest($request);

        if($formRegulier->isSubmitted() && $formRegulier->isValid()) {
            try{
                if($this->validForm($formRegulier))
                    $this->setFlash("success", "Annonce ajouter.");
                else
                    $this->setFlash("warning", "Problème inconnus.");
                return $this->redirect($this->generateUrl('annonce_covoiturage_ajout'));
            }catch(\Exception $e){
                $this->setFlash("danger", $e->getMessage());
                return $this->redirect($this->generateUrl('annonce_covoiturage_ajout'));
            }
        }else if($formUnique->isSubmitted() && $formUnique->isValid()){
            try{
                if($this->validForm($formUnique))
                    $this->setFlash("success", "Annonce ajouter.");
                else
                    $this->setFlash("warning", "Problème inconnus.");
                return $this->redirect($this->generateUrl('annonce_covoiturage_ajout'));
            }catch(\Exception $e){
                $this->setFlash("danger", $e->getMessage());
                return $this->redirect($this->generateUrl('annonce_covoiturage_ajout'));
            }
        }


        return $this->render('AnnonceBundle:covoiturage:ajout.html.twig', array(
            "formRegulier"=>$formRegulier->createView(),
            "formUnique"=>$formUnique->createView()
        ));
    }










    /**
     * @Route("/covoiturage/{id}/modifier", requirements={"id" = "\d+"}, options={"expose"=true}, name="annonce_covoiturage_modif")
     */
    public function modifAction(Request $request)
    {

        $id = $request->get('id');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AnnonceBundle:covoiturage');
        $Covoiturage = $repository->find($id);

        if(!$Covoiturage || is_null($Covoiturage))
            throw new NotFoundHttpException("Covoiturage non trouvés");

        if($Covoiturage->getAnnonce()->getAuteur()!=$this->getUser())
            throw new BadRequestHttpException("Pas le même auteur");

        if($Covoiturage->getRegulier()){
            $Covoiturage->setHeureAller($Covoiturage->getHeureAller()->format("H:i:s"));
            $Covoiturage->setHeureRetour($Covoiturage->getHeureRetour()->format("H:i:s"));
        }else{
            $Covoiturage->setDateUnique($Covoiturage->getDateUnique()->format("Y-m-d"));
            $Covoiturage->setHeureUnique($Covoiturage->getHeureUnique()->format("H:i:s"));
        }


        if($Covoiturage->getRegulier())
            $form = $this->createForm('AnnonceBundle\Form\CovoiturageTypeRegulier', $Covoiturage, array(
                'action' => $this->generateUrl('annonce_covoiturage_modif', array("id"=>$Covoiturage->getId()))));
        else
            $form = $this->createForm('AnnonceBundle\Form\CovoiturageTypeUnique', $Covoiturage, array(
                'action' => $this->generateUrl('annonce_covoiturage_modif', array("id"=>$Covoiturage->getId()))));

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {


            if($form->get('supprime')->isClicked()){
                try{
                    $em->remove($Covoiturage->getAnnonce());
                    $em->remove($Covoiturage);
                    $em->flush();
                    $this->setFlash("success", "Annonce supprimer.");
                }catch(\Exception $e){
                    $this->setFlash("warning", $e->getMessage());
                }
                
                return $this->redirect($this->generateUrl("profile_annonces"));

            };

            try{
                if($this->validForm($form))
                    $this->setFlash("success", "Annonce modifier.");
                else
                    $this->setFlash("warning", "problème inconnus.");
            }catch(\Exception $e){
                $this->setFlash("danger", $e->getMessage());
                return $this->redirect($this->generateUrl('annonce_covoiturage_modif', 
                    array("id"=>$Covoiturage->getId())));
            }


            $Covoiturage = $form->getData();

        }


        return $this->render('AnnonceBundle:covoiturage:modif.html.twig', array(
            "form"=>$form->createView(),
            "covoiturage"=>$Covoiturage
        ));
    }






    private function validForm($form){
        if ($form->isSubmitted() && $form->isValid()) {

            $covoiturage = $form->getData();


            // Recherche bonne ville
            $villeNameDepart = $covoiturage->getDepart()->getLocality();
            $villeNameArrivee = $covoiturage->getArrivee()->getLocality();
            $villeNameDepot = $covoiturage->getDepot()->getLocality();
            $villeNameRendezVous = $covoiturage->getRendezVous()->getLocality();

            $em = $this->getDoctrine()->getManager();

            try{
                
                $place = $this->getOrCreatePlace($villeNameDepart, $covoiturage->getDepart());
                $covoiturage->setDepart($place);

                $place = $this->getOrCreatePlace($villeNameArrivee, $covoiturage->getArrivee());
                $covoiturage->setArrivee($place);

                $place = $this->getOrCreatePlace($villeNameDepot, $covoiturage->getDepot());
                $covoiturage->setDepot($place);

                $place = $this->getOrCreatePlace($villeNameRendezVous, $covoiturage->getRendezVous());
                $covoiturage->setRendezVous($place);


            }catch(\Exception $e){
                throw new \Exception("Error Processing Place", 1);
                
            }

            if(!$covoiturage->getRegulier()){
                list($h,$m,$s) = explode(":", $covoiturage->getHeureUnique());
                $newDate = new \DateTime($covoiturage->getDateUnique());
                $newDate->setTime($h, $m, $s);

                $heureUnique = new \DateTime();
                $heureUnique->setTime($h, $m, $s);
                
                $covoiturage->setDateUnique($newDate);
                $covoiturage->setHeureUnique($heureUnique);
                $covoiturage->setHeureAller(null);
                $covoiturage->setHeureRetour(null);
            }else{
                $covoiturage->setHeureAller(new \DateTime($covoiturage->getHeureAller()));
                $covoiturage->setHeureRetour(new \DateTime($covoiturage->getHeureRetour()));
                // $covoiturage->setJoursAller(new \DateTime($covoiturage->getJoursAller()));
                // $covoiturage->setJoursRetour(new \DateTime($covoiturage->getJoursRetour()));
                if($covoiturage->getHeureAller() >= $covoiturage->getHeureRetour())
                    throw new Exception("L'heure d'ouverture doit etre inférieur à l'heure de fermeture");
                    
                $covoiturage->setJoursAller($covoiturage->getJoursAller());
                // $covoiturage->setJoursRetour(array_map(function($n){return $n+1;}, $covoiturage->getJoursRetour()));


                // Efface les dates réguliers
                foreach($covoiturage->getDateReguliers() as $date)
                    $covoiturage->removeDateRegulier($date);

                // Ajoute les dates réguliers
                $dateRepository = $em->getRepository("AnnonceBundle:DateRegulier");
                $dates = $dateRepository->genDatesSemaines(array_map(function($n){return $n+1;}, $covoiturage->getJoursAller()), new \Datetime(), true);
                foreach($dates as $date)
                    $covoiturage->addDateRegulier($date);

                $covoiturage->setDateUnique(null);
                $covoiturage->setHeureUnique(null);


                // echo implode(",", $covoiturage->getPreferences());
                // die();
            }


            if($form->get('modif')->isClicked()){
                try{
                    $covoiturage->getAnnonce()->updateEntity();
                    $em->persist($covoiturage->getAnnonce());
                    $em->persist($covoiturage);
                    $em->flush();
                }catch(\Exception $e){
                    throw new \Exception($e->getMessage());
                }
                
                return true;
            };


            try{
                $em->persist($covoiturage);
                $annonce = new Annonce();
                $annonce->setCovoiturage($covoiturage);
                $annonce->setAuteur($this->getUser());
                $em->persist($annonce);
                $em->flush();
            }catch(\Exception $e){
                throw new \Exception($e->getMessage());
            }

            return true;

        }else{
            throw new \Exception("formulaire non valide.");
        }
    }

















    /**
     * @Route("/covoiturage/{id}/reservation", requirements={"id" = "\d+"}, options={"expose"=true})
     */
    public function reservationAction(Request $request)
    {


        // if(!$this->getUser() || is_null($this->getUser()))
        //     return $this->redirect($this->generateUrl('fos_user_security_login'));


        $id = $request->get('id');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AnnonceBundle:Covoiturage');
        $Covoiturage = $repository->find($id);

        $reservation = new Reservation();
        $reservation->setAuteur($this->getUser())->setAnnonce($Covoiturage->getAnnonce());


        if($request->isMethod('POST')){

            // if($request->get('reservation_confirmer_online'))
            //     echo "online";
            
            // if($request->get('reservation_confirmer_surPlace'))
            //     echo "sur place";
            // die();


            // try{
            //     $villeName = $request->get('place')->getLocality();
            //     $place = $this->getOrCreatePlace($villeName, $request->get('place'));
            // }catch(Exception $e){
            //     $this->setFlash("error", "Erreur ".$e->getMessage());
            //     return $this->redirect($this->generateUrl("annonce_covoiturage_reservation", array('id'=>$Covoiturage->getId())));
            // }
                

            $prix = $request->request->get("reservation_prix");
            $distance = $request->request->get("reservation_distance");
            $nbPlaces = $request->request->get("reservation_places");
            $montant = $prix * $nbPlaces;

            
            $reservation
                ->setDepart($Covoiturage->getDepart())
                ->setArrivee($Covoiturage->getArrivee())
                ->setDistance($distance)
                ->setTypeCout('Par place')
                ->setDate(new \DateTime($request->request->get('reservation_date')))
                ->setTarif($prix)
                ->setPrix(round($montant,2))
                ->setNbPlaces($nbPlaces);

            $Covoiturage->getAnnonce()->addReservation($reservation);


            if($request->get('reservation_confirmer_online')){
            
                // Paiement
                try{
                    $createdCardRegister = $this->container->get("Paiement")->genCardPaie($this->getUser());
                }catch(Exception $e){
                    $this->setFlash("error", "Erreur ".$e->getMessage());
                    return $this->redirect($this->generateUrl("annonce_taxi_reservation", array('id'=>$Covoiturage->getId())));
                }



                // sauvegarde session

                $session = $request->getSession();
                $session->set("reservation", $reservation);
                $session->set("Covoiturage", $Covoiturage);
                $session->set("cardRegisterId", $createdCardRegister->Id);

                return $this->render('AnnonceBundle:covoiturage:paiement.html.twig', array(
                    'covoiturage'=>$Covoiturage,
                    'user'=>$this->getUser(),
                    'reservation'=>$reservation,
                    'partEntreprise'=>$this->container->get("Paiement")->getPartEntreprise()/100,
                    'percentEntreprise'=>$this->container->get("Paiement")->partEntreprise,
                    'cardUrl'=>$createdCardRegister->CardRegistrationURL,
                    'cardData'=>$createdCardRegister->PreregistrationData,
                    'accessKeyRef'=>$createdCardRegister->AccessKey,
                    'returnURL'=>$this->generateUrl('covoiturage_paiement_online', array(), true)
                ));



            }else if($request->get('reservation_confirmer_surPlace')){


                // save paiement sur place
                $paiement = new Paiement();
                $paiement->setReservation($reservation)
                    ->setMontant($reservation->getPrix())
                    ->setAuteur($this->getUser())
                    ->setTransporteur($Covoiturage->getAnnonce()->getAuteur())
                    ->setDirect(true)->setEnLigne(false);

                try{
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($paiement);
                    $em->persist($reservation);
                    $em->persist($Covoiturage);
                    $em->flush();

                    

                    // envoie notification

                    try{
                        $this->container->get("Notification")
                            ->add($Covoiturage->getAnnonce()->getAuteur(), $Covoiturage->getAnnonce(), "Nouvelle réservation de Covoiturage", "reservation", $reservation);                    
                    }catch(Exception $e){
                        $this->setFlash("danger", $e->getMessage());
                        return $this->redirect($this->generateUrl("annonce_covoiturage_reservation", array('id'=>$Covoiturage->getId())));
                    }
                    


                    // envoie SMS
                    try{
                        
                        if(!is_null($Covoiturage->getAnnonce()->getAuteur()->getTelephone()))
                            $this->container->get("Smsbox")->sendCovoiturage($Covoiturage, $reservation);
                    }catch(\Exception $e){
                        $this->setFlash("danger", $e->getMessage());
                        return $this->redirect($this->generateUrl("annonce_covoiturage_reservation", array('id'=>$Covoiturage->getId())));
                    }

                    // Envoie d'un E-mail
                    try{
                        $this->container->get('sendEmail')->setUser($Covoiturage->getAnnonce()->getAuteur())->setReservation($reservation)->reservationNouveau();
                    }catch(Exception $e){
                        $this->setFlash("danger", $e->getMessage());
                        return $this->redirect($this->generateUrl("annonce_covoiturage_reservation", array('id'=>$Covoiturage->getId())));
                    }


                    $this->setFlash("success", "Paiement direct enregistrer.");


                }catch(Exception $e){
                    $this->setFlash("danger", $e->getMessage());
                    return $this->render('AnnonceBundle:covoiturage:reservation.html.twig', array(
                        'covoiturage'=>$Covoiturage
                    ));
                }
            }


        }

        return $this->render('AnnonceBundle:covoiturage:reservation.html.twig', array(
            'covoiturage'=>$Covoiturage
        ));
    }







    /**
     * @Route("/covoiturage/paiement", name="covoiturage_paiement_online")
     */

    public function paiementAction(Request $request){


        // if(!$this->getUser() || is_null($this->getUser()))
        //     return $this->redirect($this->generateUrl('fos_user_security_login'));


        $session = $request->getSession();
        $CovoiturageData = $session->get("Covoiturage");
        $reservationData = $session->get("reservation");

        $em = $this->getDoctrine()->getManager();
        $Covoiturage = $em->getRepository("AnnonceBundle:Covoiturage")->find($CovoiturageData->getId());
        $placeDepart = $em->getRepository("GeographieBundle:Place")->find($reservationData->getDepart()->getId());
        $placeArrivee = $em->getRepository("GeographieBundle:Place")->find($reservationData->getArrivee()->getId());

        if(!$request->get("transactionId")){

            try{
                $data = $this->container->get("Paiement")
                ->setUrl($this->generateUrl("covoiturage_paiement_online", array('request'=>$request), true))
                ->setUser($this->getUser())->setRequest($request)->genPaiementClient();
            }catch(\Exception $e){
                $this->setFlash("danger", "Erreur ".$e->getMessage());
                return $this->redirect($this->generateUrl("annonce_covoiturage_reservation", array('id'=>$Covoiturage->getId())));
            }

            if($data['createdPayIn']->Status!="SUCCEEDED"){
                if($data['createdPayIn']->Status=="CREATED"){
                    return $this->redirect($data['createdPayIn']->ExecutionDetails->SecureModeRedirectURL);
                }

                $this->setFlash("danger", "Erreur ".$data['createdPayIn']->ResultMessage);
                return $this->redirect($this->generateUrl("annonce_covoiturage_reservation", array('id'=>$Covoiturage->getId())));
            }


        }else{
            
            try{
                $data = $this->container->get("Paiement")
                ->setUser($this->getUser())->setRequest($request)->getTransaction();
            }catch(\Exception $e){
                $this->setFlash("danger", "Erreur ".$e->getMessage());
                return $this->redirect($this->generateUrl("annonce_covoiturage_reservation", array('id'=>$Covoiturage->getId())));
            }

            if($data['createdPayIn']->Status!="SUCCEEDED"){
                if($data['createdPayIn']->Status=="CREATED"){
                    return $this->redirect($data['createdPayIn']->ExecutionDetails->SecureModeRedirectURL);
                }

                $this->setFlash("danger", "Erreur ".$data['createdPayIn']->ResultMessage);
                return $this->redirect($this->generateUrl("annonce_covoiturage_reservation", array('id'=>$Covoiturage->getId())));
            }

        }
            
        
        

        $reservation = new Reservation();
        $reservation->setAuteur($this->getUser())
            ->setAnnonce($Covoiturage->getAnnonce())
            ->setDepart($placeDepart)
            ->setArrivee($placeArrivee  )
            ->setDate($reservationData->getDate())
            ->setTarif($reservationData->getTarif())
            ->setPrix($reservationData->getPrix())
            ->setNbPlaces($reservationData->getNbPlaces());
        $Covoiturage->getAnnonce()->addReservation($reservation);
        

        $paiement = new Paiement();
        $paiement->setReservation($reservation)
            ->setCard($data['card']->Id)
            ->setWallet($data['createdWallet']->Id)
            ->setPayin($data['createdPayIn']->Id)
            ->setMontant($reservation->getPrix())
            ->setAuteur($this->getUser())
            ->setTransporteur($Covoiturage->getAnnonce()->getAuteur())
            ->setDirect(false)->setEnLigne(true);
        $reservation->setPaiement($paiement);

        // Sauvegarde reservation

        try{
            $em = $this->getDoctrine()->getManager();
            $em->persist($reservation);
            $em->persist($Covoiturage);
            $em->persist($paiement);
            
            $em->flush();
        }catch(Exception $e){
            $this->setFlash("error", $e->getMessage());
            return $this->redirect($this->generateUrl("annonce_covoiturage_reservation", array('id'=>$Covoiturage->getId())));
        }

        // envoie notification

        try{
            $this->container->get("Notification")
                ->add($Covoiturage->getAnnonce()->getAuteur(), $Covoiturage->getAnnonce(), "Nouvelle réservation de Covoiturage", "reservation", $reservation);                    
        }catch(Exception $e){
            $this->setFlash("danger", $e->getMessage());
            return $this->redirect($this->generateUrl("annonce_covoiturage_reservation", array('id'=>$Covoiturage->getId())));
        }
        


        // envoie SMS
        try{
            
            if(!is_null($Covoiturage->getAnnonce()->getAuteur()->getTelephone()))
                $this->container->get("Smsbox")->sendCovoiturage($Covoiturage, $reservation);
        }catch(\Exception $e){
            $this->setFlash("danger", $e->getMessage());
            return $this->redirect($this->generateUrl("annonce_covoiturage_reservation", array('id'=>$Covoiturage->getId())));
        }

        // Envoie d'un E-mail
        try{
            $this->container->get('sendEmail')->setUser($Covoiturage->getAnnonce()->getAuteur())->setReservation($reservation)->reservationNouveau();
        }catch(Exception $e){
            $this->setFlash("danger", $e->getMessage());
            return $this->redirect($this->generateUrl("annonce_covoiturage_reservation", array('id'=>$Covoiturage->getId())));
        }





        $this->setFlash("success", "Réservation effectué");
        return $this->redirect($this->generateUrl("annonce_covoiturage_reservation", array('id'=>$Covoiturage->getId())));

    }



    







    private function getOrCreatePlace($villeName, $data){
        $em = $this->getDoctrine()->getManager();
        $repositoryVille = $em->getRepository("GeographieBundle:Ville");
        $repositoryPlace = $em->getRepository("GeographieBundle:Place");

        $ville = $repositoryVille->findByName($villeName);
        if(count($ville)==0)
            throw new \Exception("Ville non trouvés", 1);

        $places = $repositoryPlace->findBy(array("place_id"=>$data->getPlaceId()));
        if(count($places)>0){
            return $places[0];
        }else{
            $place = new Place();
            $place->setVille($ville[0]);
            $place->setAdresse($data->toArray()['Adresse']);
            $place->setLocality($data->toArray()['Locality']);
            $place->setAdministrativeAreaLevel1($data->toArray()['AdministrativeAreaLevel1']);
            $place->setCountry($data->toArray()['Country']);
            $place->setPostalCode($data->toArray()['PostalCode']);
            $place->setRoute($data->toArray()['Route']);
            $place->setStreetNumber($data->toArray()['StreetNumber']);
            $place->setLatitude($data->toArray()['Latitude']);
            $place->setLongitude($data->toArray()['Longitude']);
            $place->setPlaceId($data->toArray()['PlaceId']);
            $em->persist($place);
            return $place;
        }
    }


    private function task_error($error){
        $this->setFlash("warning", $error);
        return $this->redirect($this->generateUrl('annonce_covoiturage_ajout'));
    }


    private function task_success(){
        $this->setFlash("success", "Annonce ajouter");
        return $this->redirect($this->generateUrl('annonce_covoiturage_ajout'));
    }

    /**
     * @param string $action
     * @param string $value
     */
    protected function setFlash($action, $value)
    {
        $this->container->get('session')->getFlashBag()->set($action, $value);
    }

}
