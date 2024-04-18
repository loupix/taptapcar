<?php

namespace AnnonceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use AnnonceBundle\Entity\Annonce;
use AnnonceBundle\Entity\Demenagement;
use GeographieBundle\Entity\Ville;
use GeographieBundle\Entity\Place;
use UserBundle\Entity\User;
use UserBundle\Entity\Reservation;
use UserBundle\Entity\Paiement;
use UserBundle\Entity\Devis;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class DemenagementController extends Controller
{



    /**
     * @Route("/demenagement/{id}", requirements={"id" = "\d+"}, options={"expose"=true})
     */
    public function getAction($id, Request $request)
    {   

        $id = $request->get('id');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AnnonceBundle:Demenagement');
        $Demenagement = $repository->find($id);
        if(!$Demenagement || is_null($Demenagement))
            throw new NotFoundHttpException("Pas de déménagement");
            

        return $this->render('AnnonceBundle:Demenagement:get.html.twig', array(
            'demenagement'=>$Demenagement
        ));
    }


    /**
     * @Route("/demenagement/{id}/reservation", requirements={"id" = "\d+"}, options={"expose"=true})
     */
    public function reservationAction(Request $request)
    {


        // if(!$this->getUser() || is_null($this->getUser()))
        //     return $this->redirect($this->generateUrl('fos_user_security_login'));



        $id = $request->get('id');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AnnonceBundle:Demenagement');
        $Demenagement = $repository->find($id);
        if(!$Demenagement || is_null($Demenagement))
            throw new NotFoundHttpException("Pas de déménagement");

        $reservation = new Reservation();
        $reservation->setAuteur($this->getUser())->setAnnonce($Demenagement->getAnnonce());

        if($request->isMethod('POST')){


            if($Demenagement->getTransporteur()){

                list($h, $m) = explode(":", $request->request->get('reservation_heureDepart'));
                $date = new \DateTime($request->request->get('reservation_dateDepart'));
                $date->setTime($h, $m);

                try{
                    $villeDepart = $request->get('depart')['locality'];
                    $depart = $this->getOrCreatePlaceReserv($villeDepart, $request->get('depart'));
                    $villeArrivee = $request->get('arrivee')['locality'];
                    $arrivee = $this->getOrCreatePlaceReserv($villeArrivee, $request->get('arrivee'));
                }catch(Exception $e){
                    $this->setFlash("error", "Erreur ".$e->getMessage());
                    return $this->redirect($this->generateUrl("annonce_vtc_reservation", array('id'=>$Vtc->getId())));
                }
                    
                $distance = $request->get('reservation_distance');
                $dureeTst = $request->get('reservation_duree');
                $prix = $request->request->get("reservation_prix");
                $nbPlaces = $request->request->get("reservation_places");
                $typeCout = $request->get('reservation_typeCout');

                $duree = new \DateTime();
                $duree->setTimestamp($dureeTst);

                switch ($typeCout) {
                    case 'Par kilomètres':
                        $montant = $prix * $distance;
                        break;

                    case 'Par heures':
                        $montant = $prix * $dureeTst/3600;
                        break;

                    case 'Par places':
                        $montant = $prix * $nbPlaces;
                        break;

                    case 'Prix unique':
                        $montant = $prix;
                        break;
                    
                    default:
                        $montant = $prix * $nbPlaces;
                        break;
                }

                
                $reservation
                    ->setDepart($depart)
                    ->setArrivee($arrivee)
                    ->setDistance($distance)
                    ->setDuree($duree)
                    ->setTypeCout($typeCout)
                    ->setDate($date)
                    ->setTarif($prix)
                    ->setPrix(round($montant,2))
                    ->setNbPlaces($request->request->get("reservation_places"));

                $Demenagement->getAnnonce()->addReservation($reservation);


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
                    $session->set("demenagement", $Demenagement);
                    $session->set("cardRegisterId", $createdCardRegister->Id);

                    return $this->render('AnnonceBundle:Demenagement:paiement.html.twig', array(
                        'demenagement'=>$Demenagement,
                        'user'=>$this->getUser(),
                        'reservation'=>$reservation,
                        'partEntreprise'=>$this->container->get("Paiement")->getPartEntreprise()/100,
                        'percentEntreprise'=>$this->container->get("Paiement")->partEntreprise,
                        'cardUrl'=>$createdCardRegister->CardRegistrationURL,
                        'cardData'=>$createdCardRegister->PreregistrationData,
                        'accessKeyRef'=>$createdCardRegister->AccessKey,
                        'returnURL'=>$this->generateUrl('demenagement_paiement_online', array(), true)
                    ));



                }else if($request->get('reservation_confirmer_surPlace')){
                    // save paiement sur place
                    $paiement = new Paiement();
                    $paiement->setReservation($reservation)
                        ->setMontant($reservation->getPrix())
                        ->setAuteur($this->getUser())
                        ->setTransporteur($Demenagement->getAnnonce()->getAuteur())
                        ->setDirect(true)->setEnLigne(false);

                    try{
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($paiement);
                        $em->persist($reservation);
                        $em->persist($Demenagement);
                        $em->flush();

                        

                        // envoie notification

                        try{
                            $this->container->get("Notification")
                                ->add($Demenagement->getAnnonce()->getAuteur(), $Demenagement->getAnnonce(), "Nouvelle réservation de Déménagement", "reservation", $reservation);                    
                        }catch(Exception $e){
                            $this->setFlash("danger", $e->getMessage());
                            return $this->redirect($this->generateUrl("annonce_demenagement_reservation", array('id'=>$Demenagement->getId())));
                        }
                        


                        // envoie SMS
                        try{
                            
                            if(!is_null($Demenagement->getTelephoneSociete()))
                                $this->container->get("Smsbox")->sendDemenagement($Demenagement, $reservation);
                        }catch(\Exception $e){
                            $this->setFlash("danger", $e->getMessage());
                            return $this->redirect($this->generateUrl("annonce_demenagement_reservation", array('id'=>$Demenagement->getId())));
                        }

                        // Envoie d'un E-mail
                        try{
                            $this->container->get('sendEmail')->setUser($Demenagement->getAnnonce()->getAuteur())->setReervation($reservation)->reservationNouveau();
                        }catch(Exception $e){
                            $this->setFlash("danger", $e->getMessage());
                            return $this->redirect($this->generateUrl("annonce_demenagement_reservation", array('id'=>$Demenagement->getId())));
                        }


                        $this->setFlash("success", "Paiement direct enregistrer.");


                    }catch(Exception $e){
                        $this->setFlash("danger", $e->getMessage());
                        return $this->render('AnnonceBundle:demenagement:reservation.html.twig', array(
                            'demenagement'=>$Demenagement
                        ));
                    }
                }




                return $this->render('AnnonceBundle:Demenagement:devis.html.twig', array(
                    'demenagement'=>$Demenagement
                ));


            }else{
                $distance = $request->request->get("reservation_distance");
                $dureeTst = $request->request->get("reservation_duree");
                $prix = $request->request->get("reservation_prix");
                $total = $request->request->get("reservation_total");
                $tarification = $request->request->get("reservation_tarification");

                $duree = new \DateTime();
                $duree->setTimestamp($dureeTst);


                $devis = new Devis();
                $devis->setAnnonce($Demenagement->getAnnonce())
                    ->setAuteur($this->getUser())
                    ->setClient($Demenagement->getAnnonce()->getAuteur())
                    ->setMontant($total)
                    ->setPrixUnitaire($prix)
                    ->setTypeDevis($tarification)
                    ->setDistance($distance)
                    ->setDuree($duree); // ici probleme


                $Demenagement->addDevis($devis);


                // envoie notification

                try{
                    $this->container->get("Notification")
                        ->add($Demenagement->getAnnonce()->getAuteur(), $Demenagement->getAnnonce(), "Nouveau devis de Déménagement", "devis", null, $devis);                 
                }catch(Exception $e){
                    $this->setFlash("danger", $e->getMessage());
                    return $this->redirect($this->generateUrl("annonce_demenagement_reservation", array('id'=>$Demenagement->getId())));
                }
                


                // envoie SMS
                try{
                    
                    if(!is_null($Demenagement->getTelephoneSociete()))
                        $this->container->get("Smsbox")->sendDemenagement($Demenagement, $devis);
                }catch(\Exception $e){
                    $this->setFlash("danger", $e->getMessage());
                    return $this->redirect($this->generateUrl("annonce_demenagement_reservation", array('id'=>$Demenagement->getId())));
                }

                // Envoie d'un E-mail
                try{
                    $this->container->get('sendEmail')->setUser($Demenagement->getAnnonce()->getAuteur())->setReservation($devis)->reservationNouveau();
                }catch(Exception $e){
                    $this->setFlash("danger", $e->getMessage());
                    return $this->redirect($this->generateUrl("annonce_demenagement_reservation", array('id'=>$Demenagement->getId())));
                }




                // sauvegarde devis
                try{
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($devis);
                    $em->persist($Demenagement);
                    $em->flush();
                }catch(Exception $e){
                    $this->setFlash("error", $e->getMessage());
                    return $this->redirect($this->generateUrl('annonce_demenagement_reservation', array('id'=>$Demenagement->getId())));
                }


                $this->setFlash("success", "Devis envoyés");
                return $this->redirect($this->generateUrl('annonce_demenagement_get', array('id'=>$Demenagement->getId())));


            }

        }

        return $this->render('AnnonceBundle:Demenagement:devis.html.twig', array(
            'demenagement'=>$Demenagement
        ));
    }







     /**
     * @Route("/demenagement/paiement", name="demenagement_paiement_online")
     */


    public function paiementAction(Request $request){


        // if(!$this->getUser() || is_null($this->getUser()))
        //     return $this->redirect($this->generateUrl('fos_user_security_login'));


        $session = $request->getSession();
        $demenagementData = $session->get("demenagement");
        $reservationData = $session->get("reservation");

        $em = $this->getDoctrine()->getManager();
        $Demenagement = $em->getRepository("AnnonceBundle:Demenagement")->find($demenagementData->getId());
        $depart = $em->getRepository("GeographieBundle:Place")->find($reservationData->getDepart()->getId());
        $arrivee = $em->getRepository("GeographieBundle:Place")->find($reservationData->getArrivee()->getId());

        if(!$request->get("transactionId")){

            try{
                $data = $this->container->get("Paiement")
                ->setUrl($this->generateUrl("demenagement_paiement_online", array('request'=>$request), true))
                ->setUser($this->getUser())->setRequest($request)->genPaiementClient();
            }catch(\Exception $e){
                $this->setFlash("danger", "Erreur ".$e->getMessage());
                return $this->redirect($this->generateUrl("annonce_demenagement_reservation", array('id'=>$Demenagement->getId())));
            }

            if($data['createdPayIn']->Status!="SUCCEEDED"){
                if($data['createdPayIn']->Status=="CREATED"){
                    return $this->redirect($data['createdPayIn']->ExecutionDetails->SecureModeRedirectURL);
                }

                $this->setFlash("danger", "Erreur ".$data['createdPayIn']->ResultMessage);
                return $this->redirect($this->generateUrl("annonce_demenagement_reservation", array('id'=>$Demenagement->getId())));
            }


        }else{
            
            try{
                $data = $this->container->get("Paiement")
                ->setUser($this->getUser())->setRequest($request)->getTransaction();
            }catch(\Exception $e){
                $this->setFlash("danger", "Erreur ".$e->getMessage());
                return $this->redirect($this->generateUrl("annonce_demenagement_reservation", array('id'=>$Demenagement->getId())));
            }

            if($data['createdPayIn']->Status!="SUCCEEDED"){
                if($data['createdPayIn']->Status=="CREATED"){
                    return $this->redirect($data['createdPayIn']->ExecutionDetails->SecureModeRedirectURL);
                }

                $this->setFlash("danger", "Erreur ".$data['createdPayIn']->ResultMessage);
                return $this->redirect($this->generateUrl("annonce_demenagement_reservation", array('id'=>$Demenagement->getId())));
            }

        }
            
        
        
        $reservation = new Reservation();
        $reservation->setAuteur($this->getUser())
            ->setAnnonce($Demenagement->getAnnonce())
            ->setDepart($depart)
            ->setArrivee($arrivee)
            ->setDuree($reservationData->getDuree())
            ->setDistance($reservationData->getDistance())
            ->setDate($reservationData->getDate())
            ->setTarif($reservationData->getTarif())
            ->setPrix($reservationData->getPrix())
            ->setTypeCout($reservationData->getTypeCout())
            ->setNbPlaces($reservationData->getNbPlaces());
        $Demenagement->getAnnonce()->addReservation($reservation);
        

        $paiement = new Paiement();
        $paiement->setReservation($reservation)
            ->setCard($data['card']->Id)
            ->setWallet($data['createdWallet']->Id)
            ->setPayin($data['createdPayIn']->Id)
            ->setMontant($reservation->getPrix())
            ->setAuteur($this->getUser())
            ->setTransporteur($Demenagement->getAnnonce()->getAuteur())
            ->setDirect(false)->setEnLigne(true);
        $reservation->setPaiement($paiement);

        // Sauvegarde reservation

        try{
            $em = $this->getDoctrine()->getManager();
            $em->persist($reservation);
            $em->persist($Demenagement);
            $em->persist($paiement);
            
            $em->flush();
        }catch(Exception $e){
            $this->setFlash("error", $e->getMessage());
            return $this->redirect($this->generateUrl("annonce_demenagement_reservation", array('id'=>$Demenagement->getId())));
        }




        // envoie notification

        try{
            $this->container->get("Notification")
                ->add($Demenagement->getAnnonce()->getAuteur(), $Demenagement->getAnnonce(), "Nouvelle réservation de Déménagement", "reservation", $reservation);                    
        }catch(Exception $e){
            $this->setFlash("danger", $e->getMessage());
            return $this->redirect($this->generateUrl("annonce_demenagement_reservation", array('id'=>$Demenagement->getId())));
        }
        


        // envoie SMS
        try{
            
            if(!is_null($Demenagement->getTelephoneSociete()))
                $this->container->get("Smsbox")->sendDemenagement($Demenagement, $reservation);
        }catch(\Exception $e){
            $this->setFlash("danger", $e->getMessage());
            return $this->redirect($this->generateUrl("annonce_demenagement_reservation", array('id'=>$Demenagement->getId())));
        }

        // Envoie d'un E-mail
        try{
            $this->container->get('sendEmail')->setUser($Demenagement->getAnnonce()->getAuteur())->setReservation($reservation)->reservationNouveau();
        }catch(Exception $e){
            $this->setFlash("danger", $e->getMessage());
            return $this->redirect($this->generateUrl("annonce_demenagement_reservation", array('id'=>$Demenagement->getId())));
        }





        $this->setFlash("success", "Réservation effectué");
        return $this->redirect($this->generateUrl("annonce_demenagement_reservation", array('id'=>$Demenagement->getId())));

    }






    /**
     * @Route("/demenagement/{annonceId}/devis/{devisId}", requirements={"annonceId" = "\d+"}, options={"expose"=true}, name="demenagement_devis_paiement_enligne")
     */
    public function devisAction(Request $request, $annonceId, $devisId){


        // if(!$this->getUser() || is_null($this->getUser()))
        //     return $this->redirect($this->generateUrl('fos_user_security_login'));

        $em = $this->getDoctrine()->getManager();
        $demenagement = $em->getRepository("AnnonceBundle:Demenagement")->find($annonceId);
        if(!$demenagement || is_null($demenagement))
            throw new NotFoundHttpException("Demenagement non trouvés");

        $devis = $em->getRepository("UserBundle:Devis")->find($devisId);
        if(!$devis || is_null($devis))
            throw new NotFoundHttpException("Devis non trouvés");


        $reservation = new Reservation();
        $reservation->setAuteur($this->getUser())
            ->setDepart($demenagement->getRendezVous())
            ->setArrivee($demenagement->getDepot())
            ->setDistance($devis->getDistance())
            ->setDuree($devis->getDuree())
            ->setTypeCout($devis->getTypeDevis())
            ->setDate($devis->getAnnonce()->getJoursUnique())
            ->setPrix($devis->getMontant());


        // Paiement
        try{
            $createdCardRegister = $this->container->get("Paiement")->genCardPaie($this->getUser());
        }catch(\Exception $e){
            $this->setFlash("error", "Erreur ".$e->getMessage());
            return $this->redirect($this->generateUrl("annonce_demenagement_reservation", array('id'=>$Demenagement->getId())));
        }



        // sauvegarde session

        $session = $request->getSession();
        $session->set("reservation", $reservation);
        $session->set("demenagement", $Demenagement);
        $session->set("cardRegisterId", $createdCardRegister->Id);

        return $this->render('AnnonceBundle:Demenagement:paiement.html.twig', array(
            'demenagement'=>$Demenagement,
            'user'=>$this->getUser(),
            'reservation'=>$reservation,
            'devis'=>$devis,
            'partEntreprise'=>$this->container->get("Paiement")->getPartEntreprise()/100,
            'percentEntreprise'=>$this->container->get("Paiement")->partEntreprise,
            'cardUrl'=>$createdCardRegister->CardRegistrationURL,
            'cardData'=>$createdCardRegister->PreregistrationData,
            'accessKeyRef'=>$createdCardRegister->AccessKey,
            'returnURL'=>$this->generateUrl('taxi_paiement_online', array(), true)
        ));
            

    }


    /**
     * @Route("/demenagement/{id}/devis/{devisId}/paiement", requirements={"id" = "\d+"}, options={"expose"=true})
     */
    public function devisPaiementAction(Request $request){
        $id = $request->get('id');
        $devisId = $request->get('devisId');

        $em = $this->getDoctrine()->getManager();
        $demenagement = $em->getRepository("AnnonceBundle:Demenagement")->find($id);
        if(!$demenagement || is_null($demenagement))
            throw new NotFoundHttpException("Demenagement non trouvés");

        $devis = $em->getRepository("UserBundle:Devis")->find($id);
        if(!$devis || is_null($devis))
            throw new NotFoundHttpException("Devis non trouvés");
    }

















    /**
     * @Route("/demenagement/ajout")
     */
    public function ajoutAction(Request $request)
    {


        if(!$this->getUser() || is_null($this->getUser()))
            return $this->redirect($this->generateUrl('fos_user_security_login'));

        $demTransporteur = new Demenagement();
        $demTransporteur->setTransporteur(True);

        $demNonTransporteur = new Demenagement();
        $demNonTransporteur->setTransporteur(False);

        $formTransporteur = $this->createForm('AnnonceBundle\Form\DemenagementTypeTransporteur', $demTransporteur, array(
            'action' => $this->generateUrl('annonce_demenagement_ajout')));
        $formNonTransporteur = $this->createForm('AnnonceBundle\Form\DemenagementTypeRecherche', $demNonTransporteur, array(
            'action' => $this->generateUrl('annonce_demenagement_ajout')));

        $formTransporteur->handleRequest($request);
        $formNonTransporteur->handleRequest($request);

        if($formTransporteur->isSubmitted() && $formTransporteur->isValid()) {
            try{
                if($this->validForm($formTransporteur))
                    $this->setFlash("success", "Annonce ajouter.");
                else
                    $this->setFlash("warning", "Problème inconnus.");
                return $this->redirect($this->generateUrl('annonce_demenagement_ajout'));
            }catch(\Exception $e){
                $this->setFlash("danger", $e->getMessage());
                return $this->redirect($this->generateUrl('annonce_demenagement_ajout'));
            }
        }else if($formNonTransporteur->isSubmitted() && $formNonTransporteur->isValid()){
            try{
                if($this->validForm($formNonTransporteur))
                    $this->setFlash("success", "Annonce ajouter.");
                else
                    $this->setFlash("warning", "Problème inconnus.");
                return $this->redirect($this->generateUrl('annonce_demenagement_ajout'));
            }catch(\Exception $e){
                $this->setFlash("danger", $e->getMessage());
                return $this->redirect($this->generateUrl('annonce_demenagement_ajout'));
            }
        }

        

        return $this->render('AnnonceBundle:Demenagement:ajout.html.twig', array(
            'formTransporteur'=>$formTransporteur->createView(),
            'formNonTransporteur'=>$formNonTransporteur->createView()
        ));
    }









    /**
     * @Route("/demenagement/{id}/modifier", requirements={"id" = "\d+"}, options={"expose"=true}, name="annonce_demenagement_modif")
     */
    public function modifAction(Request $request)
    {


        if(!$this->getUser() || is_null($this->getUser()))
            return $this->redirect($this->generateUrl('fos_user_security_login'));

        $id = $request->get('id');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AnnonceBundle:Demenagement');
        $Demenagement = $repository->find($id);

        if(!$Demenagement || is_null($Demenagement))
            throw new NotFoundHttpException("Demenagement non trouvés");

        if($Demenagement->getAnnonce()->getAuteur()!=$this->getUser())
            throw new BadRequestHttpException("Pas le même auteur");

        if($Demenagement->getTransporteur()){
            $Demenagement->setHeureAller($Demenagement->getHeureAller()->format("H:i:s"));
            $Demenagement->setHeureRetour($Demenagement->getHeureRetour()->format("H:i:s"));
        }else{
            $Demenagement->setJoursUnique($Demenagement->getJoursUnique()->format("Y-m-d"));
            $Demenagement->setHeureUnique($Demenagement->getHeureUnique()->format("H:i:s"));
        }



        if($Demenagement->getTransporteur())
            $form = $this->createForm('AnnonceBundle\Form\DemenagementTypeTransporteur', $Demenagement, array(
                'action' => $this->generateUrl('annonce_demenagement_modif', array("id"=>$Demenagement->getId()))));
        else
            $form = $this->createForm('AnnonceBundle\Form\DemenagementTypeRecherche', $Demenagement, array(
                'action' => $this->generateUrl('annonce_demenagement_modif', array("id"=>$Demenagement->getId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            if($form->get('supprime')->isClicked()){
                try{
                    $em->remove($Demenagement->getAnnonce());
                    $em->remove($Demenagement);
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
                    $this->setFlash("warning", "Erreur inconnus.");
            }catch(\Exception $e){
                $this->setFlash("danger", $e->getMessage());
                return $this->redirect($this->generateUrl('annonce_demenagement_modif', 
                    array("id"=>$Demenagement->getId())));
            };

        }


        return $this->render('AnnonceBundle:Demenagement:modif.html.twig', array(
            "form"=>$form->createView(),
            "demenagement"=>$Demenagement
        ));
    }

    /**
     * @Route("/demenagement/supprime")
     */
    public function supprimeAction()
    {
        return $this->render('AnnonceBundle:Demenagement:supprime.html.twig', array(
            // ...
        ));
    }















    private function validForm($form){
        if ($form->isSubmitted() && $form->isValid()) {

            $demenagement = $form->getData();

            $em = $this->getDoctrine()->getManager();

            if(!$demenagement->getTransporteur()){
                // Recherche bonne ville
                $villeNameDepart = $demenagement->getDepart()->getLocality();
                $villeNameArrivee = $demenagement->getArrivee()->getLocality();
                $villeNameDepot = $demenagement->getDepot()->getLocality();
                $villeNameRendezVous = $demenagement->getRendezVous()->getLocality();

                try{
                    $place = $this->getOrCreatePlace($villeNameDepart, $demenagement->getDepart());
                    $demenagement->setDepart($place);

                    $place = $this->getOrCreatePlace($villeNameArrivee, $demenagement->getArrivee());
                    $demenagement->setArrivee($place);

                    $place = $this->getOrCreatePlace($villeNameDepot, $demenagement->getDepot());
                    $demenagement->setDepot($place);

                    $place = $this->getOrCreatePlace($villeNameRendezVous, $demenagement->getRendezVous());
                    $demenagement->setRendezVous($place);


                }catch(\Exception $e){
                    throw new \Exception($e->getMessage());
                }
            }else{
                // recherche position actuelle
                // $geoip = $this->get('maxmind.geoip')->lookup($this->getIp());
                // if(!$geoip)
                //     throw new \Exception("Position non trouvé");
                // $lat = $geoip->getLatitude();
                // $lon = $geoip->getLongitude();

                $villeNameLieu = $demenagement->getLieu()->getLocality();

                $em = $this->getDoctrine()->getManager();
                $repositoryVille = $em->getRepository("GeographieBundle:Ville");
                $ville = $repositoryVille->findByName($villeNameLieu);
                if(count($ville)==0)
                    throw new \Exception("Ville non trouvé");

                $demenagement->setVille($ville[0]);

                $place = $this->getOrCreatePlace($villeNameLieu, $demenagement->getLieu());
                $demenagement->setLieu($place);
            }


            if(!$demenagement->getTransporteur()){

                list($h,$m,$s) = explode(":", $demenagement->getHeureUnique());
                $newDate = new \DateTime($demenagement->getJoursUnique());
                $newDate->setTime($h, $m, $s);

                $heureUnique = new \DateTime();
                $heureUnique->setTime($h, $m, $s);

                

                $demenagement->setJoursUnique($newDate);
                $demenagement->setHeureUnique($heureUnique);
                $demenagement->setHeureAller(null);
                $demenagement->setHeureRetour(null);
                $demenagement->setTransporteur(False);
            }else{
                
                $demenagement->setHeureAller(new \DateTime($demenagement->getHeureAller()));
                $demenagement->setHeureRetour(new \DateTime($demenagement->getHeureRetour()));
                if($demenagement->getHeureAller() >= $demenagement->getHeureRetour())
                    throw new \Exception("L'heure d'ouverture doit etre inférieur à l'heure de fermeture");

                $demenagement->setJoursUnique(null);
                $demenagement->setHeureUnique(null);

                $demenagement->setJoursAller($demenagement->getJoursAller());
                // $demenagement->setJoursRetour(array_map(function($n){return $n+1;}, $demenagement->getJoursRetour()));

                // Efface les dates réguliers
                foreach($demenagement->getDateReguliers() as $date)
                    $demenagement->removeDateRegulier($date);

                // Ajoute les dates réguliers
                $dateRepository = $em->getRepository("AnnonceBundle:DateRegulier");
                $dates = $dateRepository->genDatesSemaines(array_map(function($n){return $n+1;}, $demenagement->getJoursAller()), new \Datetime(), true);
                foreach($dates as $date)
                    $demenagement->addDateRegulier($date);
                $demenagement->setTransporteur(True);


            }


            if($form->get('modif')->isClicked()){
                try{
                    $demenagement->getAnnonce()->updateEntity();
                    $em->flush();
                }catch(\Exception $e){
                    throw new \Exception($e->getMessage());
                }
                return true;
            };



            try{
                $em->persist($demenagement);

                $annonce = new Annonce();
                $annonce->setDemenagement($demenagement);
                $annonce->setAuteur($this->getUser());
                
                $em->persist($annonce);
                $em->flush();
            }catch(\Exception $e){
                throw new \Exception($e->getMessage());
            }
            
            return true;

        }else{
            throw new \Exception("Formulaire non valide.");
        }
    }



    
    /**
     * @param string $villeName
     * @param object $data
    **/



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




    private function getOrCreatePlaceReserv($villeName, $data){

        $em = $this->getDoctrine()->getManager();
        $repositoryVille = $em->getRepository("GeographieBundle:Ville");
        $repositoryPlace = $em->getRepository("GeographieBundle:Place");

        $ville = $repositoryVille->findByName($villeName);
        if(count($ville)==0)
            throw new \Exception("Ville non trouvés", 1);

        $places = $repositoryPlace->findBy(array("place_id"=>$data['place_id']));
        if(count($places)>0){
            return $places[0];
        }else{
            $place = new Place();
            $place->setVille($ville[0]);
            $place->setAdresse($data['adresse']);
            $place->setLatitude($data['latitude']);
            $place->setLongitude($data['longitude']);
            $place->setPlaceId($data['place_id']);
            $em->persist($place);
            $em->flush();
            return $place;
        }
    }




    private function task_error($error){
        $this->setFlash("warning", $error);
        return $this->redirect($this->generateUrl('annonce_demenagement_ajout'));
    }


    private function task_success(){
        $this->setFlash("success", "Annonce ajouter");
        return $this->redirect($this->generateUrl('annonce_demenagement_ajout'));
    }

    /**
     * @param string $action
     * @param string $value
     */
    protected function setFlash($action, $value)
    {
        $this->container->get('session')->getFlashBag()->set($action, $value);
    }


    private function getIp(){
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

}
