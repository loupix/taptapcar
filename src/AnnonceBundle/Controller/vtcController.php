<?php

namespace AnnonceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use AnnonceBundle\Entity\vtc;
use AnnonceBundle\Entity\Annonce;
use GeographieBundle\Entity\Ville;
use GeographieBundle\Entity\Place;
use UserBundle\Entity\User;
use UserBundle\Entity\Reservation;
use UserBundle\Entity\Paiement;
use UserBundle\Entity\Societe;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;

use MangoPay\Libraries\Logs;

class vtcController extends Controller
{
    /**
     * @Route("/vtc/{id}", requirements={"id" = "\d+"}, options={"expose"=true})
     */
    public function getAction(Request $request)
    {
        $id = $request->get('id');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AnnonceBundle:vtc');
        $Vtc = $repository->find($id);

        if(!$Vtc || is_null($Vtc))
            throw new NotFoundHttpException("Vtc non trouvés");

        return $this->render('AnnonceBundle:vtc:get.html.twig', array(
            'vtc'=>$Vtc
        ));
    }


    /**
     * @Route("/vtc/{id}/reservation", requirements={"id" = "\d+"}, options={"expose"=true})
     */
    public function reservationAction(Request $request)
    {

        // if(!$this->getUser() || is_null($this->getUser()))
        //     return $this->redirect($this->generateUrl('fos_user_security_login'));

        $id = $request->get('id');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AnnonceBundle:vtc');
        $Vtc = $repository->find($id);

        $reservation = new Reservation();
        $reservation->setAuteur($this->getUser())->setAnnonce($Vtc->getAnnonce());


        if($request->isMethod('POST')){

            // if($request->get('reservation_confirmer_online'))
            //     echo "online";
            
            // if($request->get('reservation_confirmer_surPlace'))
            //     echo "sur place";
            // die();

            list($h, $m) = explode(":", $request->request->get('reservation_heureDepart'));
            $date = new \DateTime($request->request->get('reservation_dateDepart'));
            $date->setTime($h, $m);

            try{
                $villeDepart = $request->get('depart')['locality'];
                $depart = $this->getOrCreatePlace($villeDepart, $request->get('depart'));
                $villeArrivee = $request->get('arrivee')['locality'];
                $arrivee = $this->getOrCreatePlace($villeArrivee, $request->get('arrivee'));
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
                ->setPrix(round($montant, 2))
                ->setNbPlaces($request->request->get("reservation_places"));

            $Vtc->getAnnonce()->addReservation($reservation);


            if($request->get('reservation_confirmer_online')){
            
                // Paiement
                try{
                    $createdCardRegister = $this->container->get("Paiement")->genCardPaie($this->getUser());
                }catch(Exception $e){
                    $this->setFlash("error", "Erreur ".$e->getMessage());
                    return $this->redirect($this->generateUrl("annonce_vtc_reservation", array('id'=>$Vtc->getId())));
                }



                // sauvegarde session

                $session = $request->getSession();
                $session->set("reservation", $reservation);
                $session->set("Vtc", $Vtc);
                $session->set("cardRegisterId", $createdCardRegister->Id);

                return $this->render('AnnonceBundle:vtc:paiement.html.twig', array(
                    'vtc'=>$Vtc,
                    'user'=>$this->getUser(),
                    'reservation'=>$reservation,
                    'partEntreprise'=>$this->container->get("Paiement")->getPartEntreprise()/100,
                    'percentEntreprise'=>$this->container->get("Paiement")->partEntreprise,
                    'cardUrl'=>$createdCardRegister->CardRegistrationURL,
                    'cardData'=>$createdCardRegister->PreregistrationData,
                    'accessKeyRef'=>$createdCardRegister->AccessKey,
                    'returnURL'=>$this->generateUrl('vtc_paiement_online', array(), true)
                ));



            }else if($request->get('reservation_confirmer_surPlace')){

                
                // save paiement sur place
                $paiement = new Paiement();
                $paiement->setReservation($reservation)
                    ->setMontant($reservation->getPrix())
                    ->setAuteur($this->getUser())
                    ->setTransporteur($Vtc->getAnnonce()->getAuteur())
                    ->setDirect(true)->setEnLigne(false);

                try{
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($paiement);
                    $em->persist($reservation);
                    $em->persist($Vtc);
                    $em->flush();
                    

                    // envoie notification

                    try{
                        $this->container->get("Notification")
                            ->add($Vtc->getAnnonce()->getAuteur(), $Vtc->getAnnonce(), "Nouvelle réservation de VTC", "reservation", $reservation);                    
                    }catch(Exception $e){
                        $this->setFlash("danger", $e->getMessage());
                        return $this->redirect($this->generateUrl("annonce_vtc_reservation", array('id'=>$Vtc->getId())));
                    }
                    


                    // envoie SMS
                    try{
                        
                        if(!is_null($Vtc->getTelephoneSociete()))
                            $this->container->get("Smsbox")->sendVtc($Vtc, $reservation);
                    }catch(\Exception $e){
                        $this->setFlash("danger", $e->getMessage());
                        return $this->redirect($this->generateUrl("annonce_vtc_reservation", array('id'=>$Vtc->getId())));
                    }

                    // Envoie d'un E-mail
                    try{
                        $this->container->get('sendEmail')->setUser($Vtc->getAnnonce()->getAuteur())->setReservation($reservation)->reservationNouveau();
                    }catch(Exception $e){
                        $this->setFlash("danger", $e->getMessage());
                        return $this->redirect($this->generateUrl("annonce_vtc_reservation", array('id'=>$Vtc->getId())));
                    }




                    $this->setFlash("success", "Paiement direct enregistrer.");


                }catch(Exception $e){
                    $this->setFlash("danger", $e->getMessage());
                    return $this->render('AnnonceBundle:vtc:reservation.html.twig', array(
                        'vtc'=>$Vtc
                    ));
                }
            }


        }

        return $this->render('AnnonceBundle:vtc:reservation.html.twig', array(
            'vtc'=>$Vtc
        ));
    }



    /**
     * @Route("/vtc/paiement", name="vtc_paiement_online")
     */


    public function paiementAction(Request $request){


        // if(!$this->getUser() || is_null($this->getUser()))
        //     return $this->redirect($this->generateUrl('fos_user_security_login'));

        $session = $request->getSession();
        $VtcData = $session->get("Vtc");
        $reservationData = $session->get("reservation");


        $em = $this->getDoctrine()->getManager();
        $Vtc = $em->getRepository("AnnonceBundle:vtc")->find($VtcData->getId());
        $depart = $em->getRepository("GeographieBundle:Place")->find($reservationData->getDepart()->getId());
        $arrivee = $em->getRepository("GeographieBundle:Place")->find($reservationData->getArrivee()->getId());


        if(!$request->get("transactionId")){

            try{
                $data = $this->container->get("Paiement")
                ->setUrl($this->generateUrl("vtc_paiement_online", array('request'=>$request), true))
                ->setUser($this->getUser())->setRequest($request)->genPaiementClient();
            }catch(\Exception $e){
                $this->setFlash("danger", "Erreur ".$e->getMessage());
                return $this->redirect($this->generateUrl("annonce_vtc_reservation", array('id'=>$Vtc->getId())));
            }

            if($data['createdPayIn']->Status!="SUCCEEDED"){
                if($data['createdPayIn']->Status=="CREATED"){
                    return $this->redirect($data['createdPayIn']->ExecutionDetails->SecureModeRedirectURL);
                }

                $this->setFlash("danger", "Erreur ".$data['createdPayIn']->ResultMessage);
                return $this->redirect($this->generateUrl("annonce_vtc_reservation", array('id'=>$Vtc->getId())));
            }


        }else{
            
            try{
                $data = $this->container->get("Paiement")
                ->setUser($this->getUser())->setRequest($request)->getTransaction();
            }catch(\Exception $e){
                $this->setFlash("danger", "Erreur ".$e->getMessage());
                return $this->redirect($this->generateUrl("annonce_vtc_reservation", array('id'=>$Vtc->getId())));
            }

            if($data['createdPayIn']->Status!="SUCCEEDED"){
                if($data['createdPayIn']->Status=="CREATED"){
                    return $this->redirect($data['createdPayIn']->ExecutionDetails->SecureModeRedirectURL);
                }

                $this->setFlash("danger", "Erreur ".$data['createdPayIn']->ResultMessage);
                return $this->redirect($this->generateUrl("annonce_vtc_reservation", array('id'=>$Vtc->getId())));
            }

        }
            
        
        

        $reservation = new Reservation();
        $reservation->setAuteur($this->getUser())
            ->setAnnonce($Vtc->getAnnonce())
            ->setDepart($depart)
            ->setArrivee($arrivee)
            ->setDuree($reservationData->getDuree())
            ->setDistance($reservationData->getDistance())
            ->setDate($reservationData->getDate())
            ->setTarif($reservationData->getTarif())
            ->setPrix($reservationData->getPrix())
            ->setTypeCout($reservationData->getTypeCout())
            ->setNbPlaces($reservationData->getNbPlaces());
        $Vtc->getAnnonce()->addReservation($reservation);
        

        $paiement = new Paiement();
        $paiement->setReservation($reservation)
            ->setCard($data['card']->Id)
            ->setWallet($data['createdWallet']->Id)
            ->setPayin($data['createdPayIn']->Id)
            ->setMontant($reservation->getPrix())
            ->setAuteur($this->getUser())
            ->setTransporteur($Vtc->getAnnonce()->getAuteur())
            ->setDirect(false)->setEnLigne(true);
        $reservation->setPaiement($paiement);

        // Sauvegarde reservation

        try{
            $em = $this->getDoctrine()->getManager();
            $em->persist($reservation);
            $em->persist($Vtc);
            $em->persist($paiement);
            
            $em->flush();
        }catch(\Exception $e){
            $this->setFlash("danger", $e->getMessage());
            return $this->redirect($this->generateUrl("annonce_vtc_reservation", array('id'=>$Vtc->getId())));
        }

        // envoie notification

        try{
            $this->container->get("Notification")
                ->add($Vtc->getAnnonce()->getAuteur(), $Vtc->getAnnonce(), "Nouvelle réservation de VTC", "reservation", $reservation);                    
        }catch(Exception $e){
            $this->setFlash("danger", $e->getMessage());
            return $this->redirect($this->generateUrl("annonce_vtc_reservation", array('id'=>$Vtc->getId())));
        }
        


        // envoie SMS
        try{
            
            if(!is_null($Vtc->getTelephoneSociete()))
                $this->container->get("Smsbox")->sendVtc($Vtc, $reservation);
        }catch(\Exception $e){
            $this->setFlash("danger", $e->getMessage());
            return $this->redirect($this->generateUrl("annonce_vtc_reservation", array('id'=>$Vtc->getId())));
        }

        // Envoie d'un E-mail
        try{
            $this->container->get('sendEmail')->setUser($Vtc->getAnnonce()->getAuteur())->setReservation($reservation)->reservationNouveau();
        }catch(Exception $e){
            $this->setFlash("danger", $e->getMessage());
            return $this->redirect($this->generateUrl("annonce_vtc_reservation", array('id'=>$Vtc->getId())));
        }





        $this->setFlash("success", "Réservation effectué");
        return $this->redirect($this->generateUrl("annonce_vtc_reservation", array('id'=>$Vtc->getId())));

    }




















    /**
     * @Route("/vtc/ajout")
     */
    public function ajoutAction(Request $request)
    {
        $Vtc = new vtc();
        // $Societe = new Societe();
        // $Societe->setUser($this->getUser());

        // $Vtc->setSociete($Societe);

        $form = $this->createForm('AnnonceBundle\Form\VtcType', $Vtc, array(
            'action' => $this->generateUrl('annonce_vtc_ajout')));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $Vtc = $form->getData();

            // Recherche bonne ville
                $name = $Vtc->getVille()->getLocality();

            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository('GeographieBundle:Ville');


            try{
                $villes = $repository->findByName($name);

                if(count($villes)==0){
                    return $this->task_error("Villes non trouvés.");
                }

                $Vtc->setVille($villes[0]);
            }catch(Exception $e){
                return $this->task_error($e->getMessage());
            }
            
            // if($Vtc->getTelephoneSociete())
            //     $Vtc->setTelephoneSociete($this->container->get('libphonenumber.phone_number_util')
            //         ->parse($Vtc->getTelephoneSociete(), PhoneNumberUtil::UNKNOWN_REGION));
            

            list($h,$m,$s) = explode(":", $Vtc->getHeureDebut());
            $heurDebut = new \DateTime();
            $heurDebut->setTime($h, $m, $s);
            $Vtc->setHeureDebut($heurDebut);

            list($h,$m,$s) = explode(":", $Vtc->getHeureFin());
            $heurFin = new \DateTime();
            $heurFin->setTime($h, $m, $s);
            $Vtc->setHeureFin($heurFin);


            if($Vtc->getHeureDebut() >= $Vtc->getHeureFin())
                return $this->task_error("L'heure d'ouverture doit etre inférieur à l'heure de fermeture");

            // print_r($t);
            // die();
            
            // Ajout de l'annonce
            try{
                // Sauvegarde
                $em->persist($Vtc);
                $annonce = new Annonce();
                $annonce->setVtc($Vtc);
                $annonce->setAuteur($this->getUser());
                $em->persist($annonce);
            }catch(Exception $e){
                return $this->task_error($e->getMessage());
            }

            // Sauvegarde
            try{
                $em->flush();
            }catch(Exception $e){
                return $this->task_error($e->getMessage());
            }
            

            return $this->task_success();
        };



        return $this->render('AnnonceBundle:vtc:ajout.html.twig', array(
            'form'=>$form->createView()
        ));
    }


















    /**
     * @Route("/vtc/{id}/modifier", requirements={"id" = "\d+"}, options={"expose"=true}, name="annonce_vtc_modif")
     */
    public function modifAction(Request $request)
    {

        $id = $request->get('id');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AnnonceBundle:vtc');
        $Vtc = $repository->find($id);

        if(!$Vtc || is_null($Vtc))
            throw new NotFoundHttpException("Vtc non trouvés");

        if($Vtc->getAnnonce()->getAuteur()!=$this->getUser())
            throw new BadRequestHttpException("Pas le même auteur");


        $Vtc->setHeureDebut($Vtc->getHeureDebut()->format("H:i:s"));
        $Vtc->setHeureFin($Vtc->getHeureFin()->format("H:i:s"));
        

        $form = $this->createForm('AnnonceBundle\Form\VtcType', $Vtc, array(
            'action' => $this->generateUrl('annonce_vtc_modif', array("id"=>$Vtc->getId()))
            ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            // Button supprimer

            if($form->get('supprime')->isClicked()){
                try{
                    $em->remove($Vtc->getAnnonce());
                    $em->remove($Vtc);
                    $em->flush();
                    $this->setFlash("success", "Annonce supprimer.");
                }catch(\Exception $e){
                    $this->setFlash("warning", $e->getMessage());
                }
                
                return $this->redirect($this->generateUrl("profile_annonces"));

            };



            $Vtc = $form->getData();


            // Recherche bonne ville
            $name = $Vtc->getVille()->getLocality();

            try{
                $villes = $em->getRepository('GeographieBundle:Ville')->findByName($name);

                if(count($villes)==0){
                    return $this->task_error("Villes non trouvés.");
                }

                $Vtc->setVille($villes[0]);
            }catch(Exception $e){
                return $this->task_error($e->getMessage());
            }



            list($h,$m,$s) = explode(":", $Vtc->getHeureDebut());
            $heurDebut = new \DateTime();
            $heurDebut->setTime($h, $m, $s);
            $Vtc->setHeureDebut($heurDebut);

            list($h,$m,$s) = explode(":", $Vtc->getHeureFin());
            $heurFin = new \DateTime();
            $heurFin->setTime($h, $m, $s);
            $Vtc->setHeureFin($heurFin);

            if($Vtc->getHeureDebut() >= $Vtc->getHeureFin())
                throw new Exception("L'heure d'ouverture doit etre inférieur à l'heure de fermeture");

            try{
                // Sauvegarde
                $Vtc->getAnnonce()->updateEntity();
                $em->persist($Vtc->getAnnonce());
                $em->persist($Vtc);
                $em->flush();
                $this->setFlash("success", "Annonce modifier");
            }catch(\Exception $e){
                $this->setFlash("danger", $e->getMessage());
            }


        }


        return $this->render('AnnonceBundle:vtc:modif.html.twig', array(
            "form"=>$form->createView(),
            "vtc"=>$Vtc
        ));
    }










    /**
     * @Route("/vtc/supprime")
     */
    public function supprimeAction()
    {
        return $this->render('AnnonceBundle:vtc:supprime.html.twig', array(
            // ...
        ));
    }



    private function getOrCreatePlace($villeName, $data){

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
        return $this->redirect($this->generateUrl('annonce_vtc_ajout'));
    }


    private function task_success(){
        $this->setFlash("success", "Annonce ajouter");
        return $this->redirect($this->generateUrl('annonce_vtc_ajout'));
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
