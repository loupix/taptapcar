<?php

namespace AnnonceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use AnnonceBundle\Entity\Taxi;
use AnnonceBundle\Entity\Annonce;
use GeographieBundle\Entity\Ville;
use GeographieBundle\Entity\Place;
use UserBundle\Entity\User;
use UserBundle\Entity\Reservation;
use UserBundle\Entity\Paiement;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaxiController extends Controller
{   



    /**
     * @Route("/taxi/{id}", requirements={"id" = "\d+"}, options={"expose"=true})
     */
    public function getAction($id, Request $request)
    {   

        $id = $request->get('id');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AnnonceBundle:Taxi');
        $Taxi = $repository->find($id);

        if(!$Taxi || is_null($Taxi))
            throw new NotFoundHttpException("Taxi non trouvés");

        return $this->render('AnnonceBundle:Taxi:get.html.twig', array(
            'taxi'=>$Taxi
        ));
    }


    /**
     * @Route("/taxi/{id}/reservation", requirements={"id" = "\d+"}, options={"expose"=true})
     */
    public function reservationAction(Request $request)
    {


        // if(!$this->getUser() || is_null($this->getUser()))
        //     return $this->redirect($this->generateUrl('fos_user_security_login'));


        $id = $request->get('id');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AnnonceBundle:Taxi');
        $Taxi = $repository->find($id);

        $reservation = new Reservation();
        $reservation->setAuteur($this->getUser())->setAnnonce($Taxi->getAnnonce());


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
                return $this->redirect($this->generateUrl("annonce_taxi_reservation", array('id'=>$Taxi->getId())));
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

                case 'Au compteur':
                    $montant = 0;
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

            $Taxi->getAnnonce()->addReservation($reservation);


            if($request->get('reservation_confirmer_online')){
            
                // Paiement
                try{
                    $createdCardRegister = $this->container->get("Paiement")->genCardPaie($this->getUser());
                }catch(Exception $e){
                    $this->setFlash("error", "Erreur ".$e->getMessage());
                    return $this->redirect($this->generateUrl("annonce_taxi_reservation", array('id'=>$Taxi->getId())));
                }



                // sauvegarde session

                $session = $request->getSession();
                $session->set("reservation", $reservation);
                $session->set("taxi", $Taxi);
                $session->set("cardRegisterId", $createdCardRegister->Id);

                return $this->render('AnnonceBundle:Taxi:paiement.html.twig', array(
                    'taxi'=>$Taxi,
                    'user'=>$this->getUser(),
                    'reservation'=>$reservation,
                    'partEntreprise'=>$this->container->get("Paiement")->getPartEntreprise()/100,
                    'percentEntreprise'=>$this->container->get("Paiement")->partEntreprise,
                    'cardUrl'=>$createdCardRegister->CardRegistrationURL,
                    'cardData'=>$createdCardRegister->PreregistrationData,
                    'accessKeyRef'=>$createdCardRegister->AccessKey,
                    'returnURL'=>$this->generateUrl('taxi_paiement_online', array(), true)
                ));



            }else if($request->get('reservation_confirmer_surPlace')){


                // save paiement sur place
                $paiement = new Paiement();
                $paiement->setReservation($reservation)
                    ->setMontant($reservation->getPrix())
                    ->setAuteur($this->getUser())
                    ->setTransporteur($Taxi->getAnnonce()->getAuteur())
                    ->setDirect(true)->setEnLigne(false);

                try{
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($paiement);
                    $em->persist($reservation);
                    $em->persist($Taxi);
                    $em->flush();

                    

                    // envoie notification

                    try{
                        $this->container->get("Notification")
                            ->add($Taxi->getAnnonce()->getAuteur(), $Taxi->getAnnonce(), "Nouvelle réservation de Taxi", "reservation", $reservation);                    
                    }catch(Exception $e){
                        $this->setFlash("danger", $e->getMessage());
                        return $this->redirect($this->generateUrl("annonce_taxi_reservation", array('id'=>$Taxi->getId())));
                    }
                    


                    // envoie SMS
                    try{
                        
                        if(!is_null($Taxi->getTelephone()))
                            $this->container->get("Smsbox")->sendTaxi($Taxi, $reservation);
                    }catch(\Exception $e){
                        $this->setFlash("danger", $e->getMessage());
                        return $this->redirect($this->generateUrl("annonce_taxi_reservation", array('id'=>$Taxi->getId())));
                    }

                    // Envoie d'un E-mail
                    try{
                        $this->container->get('sendEmail')->setUser($Taxi->getAnnonce()->getAuteur())->setReservation($reservation)->reservationNouveau();
                    }catch(Exception $e){
                        $this->setFlash("danger", $e->getMessage());
                        return $this->redirect($this->generateUrl("annonce_taxi_reservation", array('id'=>$Taxi->getId())));
                    }


                    $this->setFlash("success", "Paiement sur place enregistrer.");


                }catch(\Exception $e){
                    $this->setFlash("danger", $e->getMessage());
                    return $this->render('AnnonceBundle:Taxi:reservation.html.twig', array(
                        'taxi'=>$Taxi
                    ));
                }
            }


        }

        return $this->render('AnnonceBundle:Taxi:reservation.html.twig', array(
            'taxi'=>$Taxi
        ));
    }






    /**
     * @Route("/taxi/paiement", name="taxi_paiement_online")
     */


    public function paiementAction(Request $request){


        // if(!$this->getUser() || is_null($this->getUser()))
        //     return $this->redirect($this->generateUrl('fos_user_security_login'));



        $session = $request->getSession();
        $taxiData = $session->get("taxi");
        $reservationData = $session->get("reservation");

        $em = $this->getDoctrine()->getManager();
        $Taxi = $em->getRepository("AnnonceBundle:Taxi")->find($taxiData->getId());
        $depart = $em->getRepository("GeographieBundle:Place")->find($reservationData->getDepart()->getId());
        $arrivee = $em->getRepository("GeographieBundle:Place")->find($reservationData->getArrivee()->getId());

        if(!$request->get("transactionId")){

            try{
                $data = $this->container->get("Paiement")
                ->setUrl($this->generateUrl("taxi_paiement_online", array('request'=>$request), true))
                ->setUser($this->getUser())->setRequest($request)->genPaiementClient();
            }catch(\Exception $e){
                $this->setFlash("danger", "Erreur ".$e->getMessage());
                return $this->redirect($this->generateUrl("annonce_taxi_reservation", array('id'=>$Taxi->getId())));
            }

            if($data['createdPayIn']->Status!="SUCCEEDED"){
                if($data['createdPayIn']->Status=="CREATED"){
                    return $this->redirect($data['createdPayIn']->ExecutionDetails->SecureModeRedirectURL);
                }

                $this->setFlash("danger", "Erreur ".$data['createdPayIn']->ResultMessage);
                return $this->redirect($this->generateUrl("annonce_taxi_reservation", array('id'=>$Taxi->getId())));
            }


        }else{
            
            try{
                $data = $this->container->get("Paiement")
                ->setUser($this->getUser())->setRequest($request)->getTransaction();
            }catch(\Exception $e){
                $this->setFlash("danger", "Erreur ".$e->getMessage());
                return $this->redirect($this->generateUrl("annonce_taxi_reservation", array('id'=>$Taxi->getId())));
            }

            if($data['createdPayIn']->Status!="SUCCEEDED"){
                if($data['createdPayIn']->Status=="CREATED"){
                    return $this->redirect($data['createdPayIn']->ExecutionDetails->SecureModeRedirectURL);
                }

                $this->setFlash("danger", "Erreur ".$data['createdPayIn']->ResultMessage);
                return $this->redirect($this->generateUrl("annonce_taxi_reservation", array('id'=>$Taxi->getId())));
            }

        }
            
        
        

        $reservation = new Reservation();
        $reservation->setAuteur($this->getUser())
            ->setAnnonce($Taxi->getAnnonce())
            ->setDepart($depart)
            ->setArrivee($arrivee)
            ->setDuree($reservationData->getDuree())
            ->setDistance($reservationData->getDistance())
            ->setDate($reservationData->getDate())
            ->setTarif($reservationData->getTarif())
            ->setPrix($reservationData->getPrix())
            ->setTypeCout($reservationData->getTypeCout())
            ->setNbPlaces($reservationData->getNbPlaces());
        $Taxi->getAnnonce()->addReservation($reservation);
        

        $paiement = new Paiement();
        $paiement->setReservation($reservation)
            ->setCard($data['card']->Id)
            ->setWallet($data['createdWallet']->Id)
            ->setPayin($data['createdPayIn']->Id)
            ->setMontant($reservation->getPrix())
            ->setAuteur($this->getUser())
            ->setTransporteur($Taxi->getAnnonce()->getAuteur())
            ->setDirect(false)->setEnLigne(true);
        $reservation->setPaiement($paiement);

        // Sauvegarde reservation

        try{
            $em = $this->getDoctrine()->getManager();
            $em->persist($reservation);
            $em->persist($Taxi);
            $em->persist($paiement);
            
            $em->flush();
        }catch(\Exception $e){
            $this->setFlash("error", $e->getMessage());
            return $this->redirect($this->generateUrl("annonce_taxi_reservation", array('id'=>$Taxi->getId())));
        }

       // envoie notification

        try{
            $this->container->get("Notification")
                ->add($Taxi->getAnnonce()->getAuteur(), $Taxi->getAnnonce(), "Nouvelle réservation de Taxi", "reservation", $reservation);                    
        }catch(Exception $e){
            $this->setFlash("danger", $e->getMessage());
            return $this->redirect($this->generateUrl("annonce_taxi_reservation", array('id'=>$Taxi->getId())));
        }
        


        // envoie SMS
        try{
            
            if(!is_null($Taxi->getTelephone()))
                $this->container->get("Smsbox")->sendTaxi($Taxi, $reservation);
        }catch(\Exception $e){
            $this->setFlash("danger", $e->getMessage());
            return $this->redirect($this->generateUrl("annonce_taxi_reservation", array('id'=>$Taxi->getId())));
        }

        // Envoie d'un E-mail
        try{
            $this->container->get('sendEmail')->setUser($Taxi->getAnnonce()->getAuteur())->setReservation($reservation)->reservationNouveau();
        }catch(Exception $e){
            $this->setFlash("danger", $e->getMessage());
            return $this->redirect($this->generateUrl("annonce_taxi_reservation", array('id'=>$Taxi->getId())));
        }





        $this->setFlash("success", "Réservation effectué");
        return $this->redirect($this->generateUrl("annonce_taxi_reservation", array('id'=>$Taxi->getId())));

    }













    
    /**
     * @Route("/taxi/ajout")
     */
    public function ajoutAction(Request $request)
    {
        $taxi = new Taxi();

        $form = $this->createForm('AnnonceBundle\Form\TaxiType', $taxi, array(
            'action' => $this->generateUrl('annonce_taxi_ajout')));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $taxi = $form->getData();

            // Recherche bonne ville
            $name = $taxi->getVille()->getLocality();

            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository('GeographieBundle:Ville');


            try{
                $villes = $repository->findByName($name);

                if(count($villes)==0){
                    $this->setFlash("warning", "Ville non trouvés");
                    return $this->redirect($this->generateUrl('annonce_taxi_ajout'));
                }


                $taxi->setVille($villes[0]);
                
            }catch(Exception $e){
                $this->setFlash("warning", $e->getMessage());
                return $this->redirect($this->generateUrl('annonce_taxi_ajout'));
            }



            list($h,$m,$s) = explode(":", $taxi->getHeureDebut());
            $heurDebut = new \DateTime();
            $heurDebut->setTime($h, $m, $s);
            $taxi->setHeureDebut($heurDebut);

            list($h,$m,$s) = explode(":", $taxi->getHeureFin());
            $heurFin = new \DateTime();
            $heurFin->setTime($h, $m, $s);
            $taxi->setHeureFin($heurFin);

            if($taxi->getHeureDebut() >= $taxi->getHeureFin())
                throw new Exception("L'heure d'ouverture doit etre inférieur à l'heure de fermeture");

            
            
            // Ajout de l'annonce
            try{
                $em->persist($taxi);
                $annonce = new Annonce();
                $annonce->setTaxi($taxi);
                $annonce->setAuteur($this->getUser());
                $em->persist($annonce);
            }catch(Exception $e){
                $this->setFlash("warning", $e->getMessage());
                return $this->redirect($this->generateUrl('annonce_taxi_ajout'));
            }

            // Sauvegarde
            try{
                $em->flush();
            }catch(Exception $e){
                $this->setFlash("warning", $e->getMessage());
                return $this->redirect($this->generateUrl('annonce_taxi_ajout'));
            }
            

            $this->setFlash("success", "Annonce rajouter.");
            return $this->redirect($this->generateUrl('annonce_taxi_ajout'));
        };

        return $this->render('AnnonceBundle:Taxi:ajout.html.twig', array(
            'form'=>$form->createView()
        ));
    }












    /**
     * @Route("/taxi/{id}/modifier", requirements={"id" = "\d+"}, options={"expose"=true}, name="annonce_taxi_modif")
     */
    public function modifAction(Request $request)
    {

        $id = $request->get('id');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AnnonceBundle:taxi');
        $Taxi = $repository->find($id);

        if(!$Taxi || is_null($Taxi))
            throw new NotFoundHttpException("Taxi non trouvés");

        if($Taxi->getAnnonce()->getAuteur()!=$this->getUser())
            throw new BadRequestHttpException("Pas le même auteur");


        $Taxi->setHeureDebut($Taxi->getHeureDebut()->format("H:i:s"));
        $Taxi->setHeureFin($Taxi->getHeureFin()->format("H:i:s"));

        $form = $this->createForm('AnnonceBundle\Form\TaxiType', $Taxi, array(
            'action' => $this->generateUrl('annonce_taxi_modif', array("id"=>$Taxi->getId()))
            ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            // Button supprimer

            if($form->get('supprime')->isClicked()){
                try{
                    $em->remove($Taxi->getAnnonce());
                    $em->remove($Taxi);
                    $em->flush();
                    $this->setFlash("success", "Annonce supprimer.");
                }catch(\Exception $e){
                    $this->setFlash("warning", $e->getMessage());
                }
                
                return $this->redirect($this->generateUrl("profile_annonces"));

            };


            $Taxi = $form->getData();

            

            // Recherche bonne ville
            $name = $Taxi->getVille()->getLocality();

            try{
                $villes = $em->getRepository('GeographieBundle:Ville')->findByName($name);

                if(count($villes)==0){
                    $this->setFlash("warning", "Villes non trouvés.");
                    return $this->redirect($this->generateUrl('annonce_taxi_modif', array("id"=>$Taxi->getId())));
                }

                $Taxi->setVille($villes[0]);
            }catch(Exception $e){
                $this->setFlash("warning", $e->getMessage());
                return $this->redirect($this->generateUrl('annonce_taxi_modif', array("id"=>$Taxi->getId())));
            }



            list($h,$m,$s) = explode(":", $Taxi->getHeureDebut());
            $heurDebut = new \DateTime();
            $heurDebut->setTime($h, $m, $s);
            $Taxi->setHeureDebut($heurDebut);

            list($h,$m,$s) = explode(":", $Taxi->getHeureFin());
            $heurFin = new \DateTime();
            $heurFin->setTime($h, $m, $s);
            $Taxi->setHeureFin($heurFin);

            if($Taxi->getHeureDebut() >= $Taxi->getHeureFin())
                throw new Exception("L'heure d'ouverture doit etre inférieur à l'heure de fermeture");
            
            try{
                // Sauvegarde
                $Taxi->getAnnonce()->updateEntity();
                $em->persist($Taxi->getAnnonce());
                $em->persist($Taxi);
                $em->flush();
                $this->setFlash("success", "Annonce modifier");
            }catch(\Exception $e){
                $this->setFlash("danger", $e->getMessage());
            }


        }


        return $this->render('AnnonceBundle:Taxi:modif.html.twig', array(
            "form"=>$form->createView(),
            "taxi"=>$Taxi
        ));
    }

    /**
     * @Route("/taxi/supprime")
     */
    public function supprimeAction()
    {
        return $this->render('AnnonceBundle:Taxi:supprime.html.twig', array(
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
        $this->setFlash("error", $error['error']);
        return $this->redirect($this->generateUrl('annonce_taxi_ajout'));
    }


    private function task_success($annonceId){
        $this->setFlash("success", "Annonce ajouter");
        return $this->redirect($this->generateUrl('annonce_taxi_ajout'));
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
