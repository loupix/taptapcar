<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException ;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

use UserBundle\Entity\Avis;
use UserBundle\Entity\Vehicule;


class ProfileController extends Controller
{
    
    /**
     * @Route("/profile/get/{id}", name="profile_get", options={"expose"=true})
     */
    public function getAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('UserBundle:User')->find($id);
        if(!$user || is_null($user))
            return $this->redirect($this->generateUrl('fos_user_security_login'));
            
        return $this->render('UserBundle:Profile:get.html.twig', array(
            'user'=>$user,
            'profile'=>$user->getProfile(),
            'vehicule'=>$user->getProfile()->getVehicule()
        ));
    }

    /**
     * @Route("/profile/index", name="profile_accueil")
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();
        if(!$user || is_null($user))
            return $this->redirect($this->generateUrl('fos_user_security_login'));
            
        return $this->render('UserBundle:Profile:index.html.twig', array(
            'user'=>$user,
            'profile'=>$user->getProfile(),
            'vehicule'=>$user->getProfile()->getVehicule()
        ));
    }

    /**
     * @Route("/profile/annonces", name="profile_annonces")
     */
    public function annoncesAction()
    {
        $user = $this->getUser();
        if(!$user || is_null($user))
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        $em = $this->getDoctrine()->getManager();
        $annonces = $em->getRepository("AnnonceBundle:Annonce")->findBy(array("auteur"=>$user), array("createdAt"=>"DESC"));
        return $this->render('UserBundle:Profile:annonces.html.twig', array(
            'user'=>$user,
            'annonces'=>$annonces

        ));
    }


    /**
     * @Route("/profile/annonce/del/{annonceId}", name="profile_annonce_del")
     */
    public function annonceDelAction($annonceId)
    {
        $user = $this->getUser();
        if(!$user || is_null($user))
            return $this->redirect($this->generateUrl('fos_user_security_login'));

        $em = $this->getDoctrine()->getManager();
        $annonce = $em->getRepository("AnnonceBundle:Annonce")->find($annonceId);
        if(!$annonce || is_null($annonce))
            throw new NotFoundHttpException();

        if($annonce->getAuteur()!=$this->getUser())
            throw new BadRequestHttpException("Mauvais user");

        
        try{
            $em->remove($annonce->getObject());
            $em->remove($annonce);
            $em->flush();
        }catch(Exception $e){
            $this->setFlash("error", "Erreur ".$e->getMessage());
            return $this->redirect($this->generateUrl("profile_annonces"));
        }

        return $this->redirect($thiss->generateUrl('profile_annonces'));
    }

    /**
     * @Route("/profile/reservations", name="profile_reservations")
     */
    public function reservationsAction()
    {
        $user = $this->getUser();
        if(!$user || is_null($user))
            return $this->redirect($this->generateUrl('fos_user_security_login'));

        $reservations = $user->getReservations();
        $devisClient = $user->getClientDevis();
        $devisAuteur = $user->getAuteurDevis();

        $devisAccepter = array_filter($devisClient->toArray(), function($d){return !$d->getEnCours() && $d->getAccepter();});
        $devisRefuser = array_filter($devisClient->toArray(), function($d){return !$d->getEnCours() && $d->getRefuser();});
        $devisClient = array_filter($devisClient->toArray(), function($d){return $d->getEnCours();});

        $mesReservations = array_filter($reservations->toArray(), function($r) use ($user){return $r->getAuteur() == $user;});
        $confirmes = array_filter($reservations->toArray(), function($r){return ($r->getValider() && !$r->getEnCours());});
        $nonConfirmes = array_filter($reservations->toArray(), function($r){return (!$r->getEnCours() && !$r->getValider());});
        $attentes = array_filter($reservations->toArray(), function($r){return $r->getEnCours();});

        return $this->render('UserBundle:Profile:reservations.html.twig', array(
            'user'=>$user,
            'mesReservations'=>$mesReservations,
            'confirmes'=>$confirmes,
            'nonConfirmes'=>$nonConfirmes,
            'devisClient'=>$devisClient,
            'devisAuteur'=>$devisAuteur,
            'devisAccepter'=>$devisAccepter,
            'devisRefuser'=>$devisRefuser,
            'attentes'=>$attentes
        ));
    }



    /**
     * @Route("/profile/avis", name="profile_avis")
     */
    public function avisAction()
    {

        $user = $this->getUser();
        if(!$user || is_null($user))
            return $this->redirect($this->generateUrl('fos_user_security_login'));

        $form = $this->createForm('UserBundle\Form\AvisType', new Avis());

        return $this->render('UserBundle:Profile:avis.html.twig', array(
            'user'=>$user,
            'avis'=>$user->getClientAvis(),
            'form'=>$form->createView()
        ));
    }

    /**
     * @Route("/profile/paiements", name="profile_paiements")
     */
    public function paiementsAction()
    {

        $user = $this->getUser();
        if(!$user || is_null($user))
            return $this->redirect($this->generateUrl('fos_user_security_login'));

        $em = $this->getDoctrine()->getManager();
        $paiements = $em->getRepository("UserBundle:Paiement")->findBy(array('transporteur'=>$user));
        $devisClient = $em->getRepository("UserBundle:Devis")->findBy(array('client'=>$user));
        $devisAuteur = $em->getRepository("UserBundle:Devis")->findBy(array('auteur'=>$user));

        // $enCours = array_filter($paiements->toArray(), function($p){return $p->getEncours();});
        // $effectués = array_filter($paiements->toArray(), function($p){return $p->getEncours();});

        return $this->render('UserBundle:Profile:paiements.html.twig', array(
            'user'=>$user,
            'paiements'=>$paiements,
            'devisAuteur'=>$devisAuteur,
            'devisClient'=>$devisClient
        ));
    }











    /**
     * @Route("/profile/paiement/{reservationId}_{paiementId}", name="profile_paiement_confirmation")
     */
    public function paiementConfirmationAction(Request $request, $reservationId, $paiementId)
    {


        $user = $this->getUser();
        if(!$user || is_null($user))
            return $this->redirect($this->generateUrl('fos_user_security_login'));

        $em = $this->getDoctrine()->getManager();
        $reservation = $em->getRepository('UserBundle:Reservation')->find($reservationId);


        if(!$reservation || is_null($reservation))
            throw new BadRequestHttpException("Pas de reservation a cette id");

        $paiement = $em->getRepository('UserBundle:Paiement')->find($paiementId);

        if(!$paiement || is_null($paiement))
            throw new BadRequestHttpException("Pas de paiement a cette id");
        
        if($paiement->getEnLigneRegler())
            throw new BadRequestHttpException("Déjà régler.");
        
        if($paiement->getTransporteur() != $this->getUser())
            throw new BadRequestHttpException("Mauvais utilisateur.");


        

        if($request->isMethod('POST')){
            try{
                $createdPayRegister = $this->container->get("Paiement")->genPaiementFournisseur();
            }catch(Exception $e){
                $this->setFlash("error", "Erreur ".$e->getMessage());
                return $this->redirect($this->generateUrl("profile_paiements"));
            }

            return $this->render('UserBundle:Profile:paiementOnline.html.twig', array(

            ));


        }else{

            // Paiement
            try{
                $createdCardRegister = $this->container->get("Paiement")->genCardPaie($this->getUser());
            }catch(Exception $e){
                $this->setFlash("error", "Erreur ".$e->getMessage());
                return $this->redirect($this->generateUrl("profile_paiements"));
            }

            $session = $request->getSession();
            $session->set("reservation", $reservation);
            $session->set("paiement", $paiement);
            $session->set("cardRegisterId", $createdCardRegister->Id);


            return $this->render('UserBundle:Profile:paiementOnline.html.twig', array(
                'user'=>$user,
                'paiement'=>$paiement,
                'reservation'=>$reservation,
                'objet'=>$reservation->getAnnonce()->getObject(),
                'cardUrl'=>$createdCardRegister->CardRegistrationURL,
                'cardData'=>$createdCardRegister->PreregistrationData,
                'accessKeyRef'=>$createdCardRegister->AccessKey,
                'returnURL'=>$this->generateUrl('profile_paiement_confirmation_online', array('reservationId'=>$reservation->getId(), 'paiementId'=>$paiement->getId()), true)
            ));
        }
    }



    /**
     * @Route("/profile/paiementOnline/{reservationId}/{paiementId}", name="profile_paiement_confirmation_online")
     */
    public function paiementOnlineAction(Request $request, $reservationId, $paiementId){
        $session = $request->getSession();

        $em = $this->getDoctrine()->getManager();
        $reservation = $em->getRepository('UserBundle:Reservation')->find($reservationId);
        $paiement = $em->getRepository('UserBundle:Paiement')->find($paiementId);

        if(!$reservation || is_null($reservation))
            throw new BadRequestHttpException("Pas de reservation a cette id");

        if(!$paiement || is_null($paiement))
            throw new BadRequestHttpException("Pas de paiement a cette id");



        try{
            $data = $this->container->get("Paiement")
            ->setUser($this->getUser())->setPaiement($paiement)->setRequest($request)->genPaiementFournisseur();
        }catch(Exception $e){
            $this->setFlash("error", "Erreur ".$e->getMessage());
            return $this->redirect($this->generateUrl('profile_paiement_confirmation', array('reservationId'=>$reservation->getId(), 'paiementId'=>$paiement->getId())));
        }


        if($data->Status!="CREATED" || $data->Status!="SUCCESS"){
            $this->setFlash("error", "Error ".$data->ResultMessage);
            return $this->redirect($this->generateUrl('profile_paiement_confirmation', array('reservationId'=>$reservation->getId(), 'paiementId'=>$paiement->getId())));
        }

        return $this->redirect($this->generateUrl("profile_paiements"));

    }










    /**
     * @Route("/profile/devis/{annonceId}_{devisId}", name="profile_devis_get")
     */
    public function devisAfficherAction(Request $request, $annonceId, $devisId)
    {


        $user = $this->getUser();
        if(!$user || is_null($user))
            return $this->redirect($this->generateUrl('fos_user_security_login'));

        $em = $this->getDoctrine()->getManager();
        $annonce = $em->getRepository('AnnonceBundle:Annonce')->find($annonceId);


        if(!$annonce || is_null($annonce))
            throw new BadRequestHttpException("Pas d'annonce a cette id");

        $devis = $em->getRepository('UserBundle:Devis')->find($devisId);

        if(!$devis || is_null($devis))
            throw new BadRequestHttpException("Pas de paiement a cette id");
        
        if($devis->getClient() != $this->getUser())
            throw new BadRequestHttpException("Mauvais utilisateur.");

        if($request->isMethod('POST')){
            if($request->request->get("reservation_supprimer")){
                $devis->setAccepter(false)
                    ->setRefusee(true)
                    ->setUpdatedAt(new \DateTime());
                try{
                    $em->persist($devis);
                    $em->flush();
                }catch(\Exception $e){
                    throw new \Exception($e->getMessage());
                }

                // Envoie notification

                try{
                    $this->container->get("Notification")
                        ->add($devis->getAuteur(), $devis->getAnnonce(), "Votre devis a été refusé.");                    
                }catch(\Exception $e){
                    $this->setFlash("danger", $e->getMessage());
                    return $this->redirect($this->generateUrl("profile_devis_get", 
                        array('annonceId'=>$annonce->getId(), "devisId"=>$devis->getId())));
                }


                // Envoie d'un message
                // try{
                //     $this->container->get('sendEmail')->setUser($Taxi->getAnnonce()->getAuteur())
                //         ->sendMailDevisRefusee($devis);
                // }catch(\Exception $e){
                //     $this->setFlash("danger", $e->getMessage());
                //     return $this->redirect($this->generateUrl("annonce_taxi_reservation", array('id'=>$Taxi->getId())));
                // }

                $this->setFlash("success", "Devis refusée.");
                return $this->redirect($this->generateUrl("profile_paiements"));



            }else{


                // et ??

                // il faut creer une réservation 
                // et rediriger vers paiement en ligne !!

                $Demenagement = $annonce->getObject();

                if($request->request->get("reservation_confirmer_direct")){


                    $reservation = new Reservation();
                    $reservation->setAuteur($user)
                        ->setAnnonce($annonce)
                        ->setDepart($demenagement->getRendezVous())
                        ->setArrivee($demenagement->getDepot())
                        ->setDuree($devis->getDuree())
                        ->setDistance($devis->getDistance())
                        ->setDate($devis->getAnnonce()->getJoursUnique())
                        ->setPrix($devis->getMontant())
                        ->setTypeCout($devis->getTypeDevis());


                    // save paiement sur place
                    $paiement = new Paiement();
                    $paiement->setReservation($reservation)
                        ->setMontant($reservation->getPrix())
                        ->setAuteur($this->getUser())
                        ->setTransporteur($devis->getAuteur())
                        ->setDirect(true)->setEnLigne(false);

                    try{
                        $em->persist($annonce);
                        $em->persist($paiement);
                        $em->persist($reservation);
                        $em->persist($Demenagement);
                        $em->flush();


                        // envoie notification

                        try{
                            $this->container->get("Notification")
                                ->add($Demenagement->getAnnonce()->getAuteur(), $annonce, "Nouvelle réservation de Demenagement");                    
                        }catch(\Exception $e){
                            $this->setFlash("danger", $e->getMessage());
                            return $this->redirect($this->generateUrl("profile_devis_get", 
                                array('annonceId'=>$annonce->getId(), "devisId"=>$devis->getId())));
                        }
                        


                        // envoie SMS
                        // plus tard


                        // Envoie d'un message
                        try{
                            $this->container->get('sendEmail')->setUser($Taxi->getAnnonce()->getAuteur())
                                ->sendMailReservation($reservation);
                        }catch(\Exception $e){
                            $this->setFlash("danger", $e->getMessage());
                            return $this->redirect($this->generateUrl("profile_devis_get", 
                                array('annonceId'=>$annonce->getId(), "devisId"=>$devis->getId())));
                        }


                        $this->setFlash("success", "Paiement direct enregistrer.");

                    }catch(\Exception $e){
                        $this->setFlash("danger", $e->getMessage());
                        return $this->redirect($this->generateUrl("profile_devis_get", 
                            array('annonceId'=>$annonce->getId(), "devisId"=>$devis->getId())));
                    }

                    return $this->redirect($this->generateUrl("profile_paiements"));

                }else if($request->request->get("reservation_confirmer_online")){

                    return $this->redirect($this->generateUrl("demenagement_devis_paiement_enligne", 
                        array("id"=>$Demenagement->getId(), "devisId"=>$devis->getId())));

                };

            
            };
        }

        return $this->render('UserBundle:Profile:getDevis.html.twig', array(
            "devis"=>$devis,
            "annonce"=>$annonce
        ));
    }












    /**
     * @Route("/profile/notifications", name="profile_notifications")
     */
    public function notificationsAction()
    {

        $user = $this->getUser();
        if(!$user || is_null($user))
            return $this->redirect($this->generateUrl('fos_user_security_login'));

        try{
            $notifs = $this->container->get('notification')->getNew($user);
        }catch(\Exception $e){
            throw new BadRequestHttpException($e->getMessage());
        }
        
        $notifRendu = array_map(function($o){return clone $o;}, $notifs);

        foreach($notifs as $n)
            $this->container->get('notification')->setLue($n->getId());
        

        // $notifs = $user->getNotifications();

        return $this->render('UserBundle:Profile:notifications.html.twig', array(
            'user'=>$user,
            'notifications'=>$notifRendu
        ));
    }



    /**
     * @Route("/profile/modificationUser/user", name="profile_modification_user")
     */
    public function modificationUserAction(Request $request)
    {
        $user = $this->getUser();
        if(!$user || is_null($user))
            return $this->redirect($this->generateUrl('fos_user_security_login'));


        $form = $this->createForm('UserBundle\Form\UserType', $user, array(
            'action' => $this->generateUrl('profile_modification_user')));


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            try{
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $this->setFlash("success", "Utilisateur modifiés.");

            }catch(\Exception $e){
                throw new  ConflictHttpException($e->getMessage());
            }
        }

        return $this->render('UserBundle:Profile:modificationUser.html.twig', array(
            'user'=>$user,
            'form'=>$form->createView()
        ));
    }

    /**
     * @Route("/profile/modification/profile", name="profile_modification_profile")
     */
    public function modificationProfileAction(Request $request)
    {
        $user = $this->getUser();
        if(!$user || is_null($user))
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        // if (!is_object($user) || !$user instanceof UserInterface) {
        //     throw new AccessDeniedException('This user does not have access to this section.');
        // }

        $profile = $user->getProfile();
        $vehicule = $profile->getVehicule();

        // print_r($profile->getVille()->toArray());
        // die();
        // $notations = $profile;


        $form = $this->createForm('UserBundle\Form\ProfileType', $profile, array(
            'action' => $this->generateUrl('profile_modification_profile')));


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profile = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository('GeographieBundle:Ville');


            try{

                if($profile->getVille()){
                    // Recherche bonne ville
                    $name = $profile->getVille()->getLocality();
                    $villes = $repository->findByName($name);

                    if(count($villes)==0)
                        throw new NotAcceptableHttpException("Erreur ville");

                    $profile->setVille($villes[0]);
                }

                // print_r($profile->getVille()->toArray());
                // die();
                

                // Sauvegarde
                try{
                    $v = $profile->getVehicule();
                    if(is_array($v)){
                        $vehicule = new Vehicule();
                        $vehicule->setModele($v['modele'])
                            ->setMarque($v['marque'])->setType($v['type'])
                            ->setPlaces($v['places'])->setKilometrage($v['kilometrage'])
                            ->setLongueur($v['longueur'])->setLargeur($v['largeur'])
                            ->setHauteur($v['hauteur'])->setVolume($v['volume']);
                            
                        $vehicule->setProfile($profile);
                        $em->persist($vehicule);
                        $profile->setVehicule($vehicule);
                    }else{
                        $em->persist($v);
                    }
                    
                    $em->persist($profile);
                    $em->flush();

                    $this->setFlash("success", "Profile modifiés.");

                }catch(Exception $e){
                    throw new  ConflictHttpException($e->getMessage());
                }
            }catch(\Exception $e){
                throw new  ConflictHttpException($e->getMessage());
            }
        }

        return $this->render('UserBundle:Profile:modificationProfile.html.twig', array(
            'form'=>$form->createView(),
            'profile'=>$profile,
            'vehicule'=>$vehicule
        ));
    }

    /**
     * @Route("/profile/gestion", name="profile_gestion")
     */
    public function gestionAction()
    {
        return $this->render('UserBundle:Profile:gestion.html.twig', array(
            // ...
        ));
    }



    /**
     * @Route("/profile/ajout_photo", name="profile_gestion")
     */
    public function ajoutPhotoAction()
    {

        $user = $this->getUser();

        return new Response($serializer->encode($data, $this->getRequest()->get('_format')));
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
