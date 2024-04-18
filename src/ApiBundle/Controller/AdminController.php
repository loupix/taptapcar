<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\Response;



use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AdminController extends Controller
{
    /**
     * @Route("/admin/users")
     */
    public function usersAction(Request $request)
    {

        try{

            $params = json_decode($this->get("request")->getContent(), true);

            $first_result = $params['_page'] * $params['_size'];
            $max_results = $params['_size'];
            $order = $params['_order'];


            $em = $this->getDoctrine()->getManager();
            $nbUsers = $em->getRepository('UserBundle:User')->createQueryBuilder('u')
                ->select("COUNT(u.id)")->getQuery()
                ->getSingleScalarResult();

            $users = $em->getRepository('UserBundle:User')->createQueryBuilder('u')
                ->addOrderBy("u.createdAt", $order)
                ->setFirstResult($first_result)
                ->setMaxResults($max_results)
                ->getQuery()->getResult();


            $data = array(
                "users"=>array_map(function($u){return $u->toArray(true);}, $users),
                "maxResult"=>$nbUsers,
                "params"=>$params
            );


            return $this->renderAjax($request, $data);
        }catch(Exception $e){
            throw new HttpException($e->getMessage());
        }

    }



    /**
     * @Route("/admin/user/find", options={"expose"=true})
     */
    public function usersFindAction(Request $request)
    {

        try{

            $params = json_decode($this->get("request")->getContent(), true);


            $terme = $params['terme'];

            $em = $this->getDoctrine()->getManager();

            // $query = $em->getRepository('UserBundle:User')->createQueryBuilder('u')
            //     ->join("u.profile", "p")
            //     ->join("p.ville", "v")
            //     ->where("u.nom LIKE :terme")
            //     ->orWhere("u.prenom LIKE :terme")
            //     ->orWhere("u.email LIKE :terme")
            //     ->orWhere("v.locality LIKE :terme")
            //     ->orWhere("v.country LIKE :terme")
            //     ->orWhere("v.postal_code LIKE :terme")
            //     ->orWhere("p.telephone LIKE :terme")
            //     ->setParameter("terme", '%'.$terme.'%')
            //     ->getQuery();


            $users = $em->getRepository('UserBundle:User')->createQueryBuilder('u')
                ->getQuery()->getResult();

            // echo $query->getSQL();
                    // echo $query->getParameters();

            $results = array_filter($users, function($u) use ($terme){
                $profile = $u->getProfile();
                $ville = $profile->getVille();
                if($ville)
                    $Ville_test = (stristr($ville->getPostalCode(),$terme) || stristr($ville->getLocality(),$terme));
                else
                    $Ville_test = false;



                $nom_test = stristr($u->getNom(),$terme);
                return (stristr($u->getNom(),$terme) || stristr($u->getPrenom(),$terme) || stristr($u->getEmail(),$terme)  || stristr($u->getProfile()->getTelephone(),$terme) || $Ville_test); 
            });


            $data = array(
                "users"=>array_map(function($u){return $u->toArray(true);}, $results),
                "terme"=>$terme
            );


            return $this->renderAjax($request, $data);
        }catch(Exception $e){
            throw new HttpException($e->getMessage());
        }

    }



    /**
     * @Route("/admin/user/del", options={"expose"=true})
     */
    public function userDelAction(Request $request){
        try{

            $params = json_decode($this->get("request")->getContent(), true);

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('UserBundle:User')->find($params['uid']);
            if(is_null($user) || !$user)
                throw new Exception("Pas d'user", 1);

            // send email
            try{
                $this->container->get('sendEmail')->setUser($user)->compteSupprime();
            }catch(\Exception $e){
                $this->setFlash("danger", $e->getMessage());
                return $this->redirect($this->generateUrl("annonce_taxi_reservation", array('id'=>$Taxi->getId())));
            }
            
            $em->remove($user);
            $em->flush();


            return $this->usersAction($request);


        }catch(Exception $e){
            throw new HttpException($e->getMessage());
        }
    }


    /**
     * @Route("/admin/user/change", options={"expose"=true})
     */
    public function userChangeAction(Request $request){
        try{

            $params = json_decode($this->get("request")->getContent(), true);

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('UserBundle:User')->find($params['uid']);
            if(is_null($user) || !$user)
                throw new Exception("Pas d'user", 1);
            
            if($user->hasRole("ROLE_ADMIN")){
                $user->removeRole("ROLE_ADMIN");
                $this->container->get("notification")->add($user, null, "Vous n'étes plus Administrateur.");
            }else{
                $user->addRole("ROLE_ADMIN");
                $this->container->get("notification")->add($user, null, "Vous êtes désormais Administrateur.");
            }

            // $em->persist($user);
            $em->flush();



            return $this->usersAction($request);


        }catch(Exception $e){
            throw new HttpException($e->getMessage());
        }
    }




    /**
     * @Route("/admin/user/verifie", options={"expose"=true})
     */
    public function userVerifieAction(Request $request){
        try{

            $params = json_decode($this->get("request")->getContent(), true);

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('UserBundle:User')->find($params['uid']);
            if(is_null($user) || !$user)
                throw new Exception("Pas d'user", 1);
            
            if($user->getVerifie()){
                $user->setVerifie(false);
                $this->container->get("notification")->add($user, null, "Vous n'êtes plus un utilisateur vérifiés.");
            }else{
                $user->setVerifie(true);
                $this->container->get("notification")->add($user, null, "Vous êtes désormais un utilisateur vérifiés.");
                $this->container->get("sendEmail")->setUser($user)->compteVerifie();
            }

            // $em->persist($user);
            $em->flush();



            return $this->usersAction($request);


        }catch(Exception $e){
            throw new HttpException($e->getMessage());
        }
    }






    /**
     * @Route("/admin/annonces")
     */
    public function annoncesAction(Request $request)
    {
        try{

            $params = json_decode($this->get("request")->getContent(), true);

            $first_result = $params['_page'] * $params['_size'];
            $max_results = $params['_size'];
            $order = $params['_order'];

            $em = $this->getDoctrine()->getManager();
            $nbAnnonces = $em->getRepository('AnnonceBundle:Annonce')->createQueryBuilder('a')
                ->select("COUNT(a.id)")->getQuery()
                ->getSingleScalarResult();

            $annonces = $em->getRepository('AnnonceBundle:Annonce')->createQueryBuilder('a')
                ->join("a.auteur", "u")
                ->addOrderBy("a.createdAt", $order)
                ->setFirstResult($first_result)
                ->setMaxResults($max_results)
                ->getQuery()->getResult();


            $data = array(
                "annonces"=>array_map(function($o){return $o->toArray(true);}, $annonces),
                "maxResult"=>$nbAnnonces,
                "params"=>$params
            );


            return $this->renderAjax($request, $data);
        }catch(Exception $e){
            throw new HttpException($e->getMessage());
        }
    }



    /**
     * @Route("/admin/annonce/del")
     */
    public function annonceDelAction(Request $request)
    {
        try{
            $params = json_decode($this->get("request")->getContent(), true);

            $em = $this->getDoctrine()->getManager();
            $annonce = $em->getRepository('AnnonceBundle:Annonce')->find($params['aid']);

            if(is_null($annonce) || !$annonce)
                throw new Exception("Pas d'annonce", 1);
            

            $user = $annonce->getAuteur();
            $em->remove($annonce);
            $em->flush();

            $this->container->get("notification")->add($user, null, "Votre annonce a été supprimés.");

            return $this->annoncesAction($request);


        }catch(Exception $e){
            throw new HttpException($e->getMessage());
        }
    }



    /**
     * @Route("/admin/annonce/actif")
     */
    public function annonceActifAction(Request $request)
    {
        try{
            $params = json_decode($this->get("request")->getContent(), true);

            $em = $this->getDoctrine()->getManager();
            $annonce = $em->getRepository('AnnonceBundle:Annonce')->find($params['aid']);

            if(is_null($annonce) || !$annonce)
                throw new Exception("Pas d'annonce", 1);

            $user = $annonce->getAuteur();
            
            if($annonce->getActif()){
                $annonce->setActif(false);
                $this->container->get("sendEmail")->setUser($user)->setAnnonce($annonce)->annonceRefusee();
                $this->container->get("notification")->add($user, $annonce, "Votre annonce n'est plus actif.");
            }else{
                $annonce->setActif(true);
                $this->container->get("sendEmail")->setUser($user)->setAnnonce($annonce)->annonceValidee();
                $this->container->get("notification")->add($user, $annonce, "Votre annonce a été activés.");
            }
                

            $em->persist($annonce);
            $em->flush();
            
            return $this->annoncesAction($request);


        }catch(Exception $e){
            throw new HttpException($e->getMessage());
        }
    }









    /**
     * @Route("/admin/reservations")
     */
    public function reservationsAction(Request $request)
    {
        try{

            $params = json_decode($this->get("request")->getContent(), true);

            $first_result = $params['_page'] * $params['_size'];
            $max_results = $params['_size'];
            $order = $params['_order'];

            $em = $this->getDoctrine()->getManager();
            $nbReservations = $em->getRepository('UserBundle:Reservation')->createQueryBuilder('r')
                ->select("COUNT(r.id)")->getQuery()
                ->getSingleScalarResult();

            $reservations = $em->getRepository('UserBundle:Reservation')->createQueryBuilder('r')
                ->orderBy("r.createdAt", $order)
                ->setFirstResult($first_result)
                ->setMaxResults($max_results)
                ->getQuery()->getResult();


            $data = array(
                "reservations"=>array_map(function($o){return $o->toArray();}, $reservations),
                "maxResult"=>$nbReservations,
                "params"=>$params
            );


            return $this->renderAjax($request, $data);
        }catch(Exception $e){
            throw new HttpException($e->getMessage());
        }
    }

    /**
     * @Route("/admin/paiements")
     */
    public function paiementsAction(Request $request)
    {
        try{

            $params = json_decode($this->get("request")->getContent(), true);

            $first_result = $params['_page'] * $params['_size'];
            $max_results = $params['_size'];
            $order = $params['_order'];

            $em = $this->getDoctrine()->getManager();
            $nbPaiements = $em->getRepository('UserBundle:Paiement')->createQueryBuilder('p')
                ->select("COUNT(p.id)")->getQuery()
                ->getSingleScalarResult();

            $paiements = $em->getRepository('UserBundle:Paiement')->createQueryBuilder('p')
                ->orderBy("p.createdAt", $order)
                ->setFirstResult($first_result)
                ->setMaxResults($max_results)
                ->getQuery()->getResult();


            $data = array(
                "paiements"=>array_map(function($o){return $o->toArray();}, $paiements),
                "maxResult"=>$nbPaiements,
                "params"=>$params
            );


            return $this->renderAjax($request, $data);
        }catch(Exception $e){
            throw new HttpException($e->getMessage());
        }
    }








    /**
     * @Route("/admin/contacts")
     */
    public function contactsAction(Request $request)
    {
        try{

            $params = json_decode($this->get("request")->getContent(), true);

            $first_result = $params['_page'] * $params['_size'];
            $max_results = $params['_size'];
            $order = $params['_order'];

            $em = $this->getDoctrine()->getManager();
            $nbContacts = $em->getRepository('AppBundle:contact')->createQueryBuilder('c')
                ->select("COUNT(c.id)")->getQuery()
                ->getSingleScalarResult();

            $contacts = $em->getRepository('AppBundle:contact')->createQueryBuilder('c')
                ->orderBy("c.createdAt", $order)
                ->setFirstResult($first_result)
                ->setMaxResults($max_results)
                ->getQuery()->getResult();


            $data = array(
                "contacts"=>array_map(function($o){return $o->toArray();}, $contacts),
                "maxResult"=>$nbContacts,
                "params"=>$params
            );


            return $this->renderAjax($request, $data);
        }catch(Exception $e){
            throw new HttpException($e->getMessage());
        }
    }



    /**
     * @Route("/admin/contact/del")
     */
    public function contactDelAction(Request $request)
    {
        try{
            $params = json_decode($this->get("request")->getContent(), true);

            $em = $this->getDoctrine()->getManager();
            $contacts = $em->getRepository('AppBundle:contact')->find($params['cid']);

            if(is_null($contacts) || !$contacts)
                throw new Exception("Pas de contacts", 1);
            
            $em->remove($contact);
            $em->flush();
            return $this->contactsAction($request);


        }catch(Exception $e){
            throw new HttpException($e->getMessage());
        }
    }








    /**
     * @Route("/admin/erreurs")
     */
    public function erreursAction(Request $request)
    {
        return $this->render('ApiBundle:Admin:erreurs.html.twig', array(
            // ...
        ));
    }





    /**
     * @Route("/admin/fees/parts")
     */
    public function partsAction(Request $request){
        try{
            $parts = $this->container->get("Paiement")->getParts();
            return $this->renderAjax($request, $parts);
        }catch(Exception $e){
            throw new HttpException($e->getMessage());
        }
    }



    private function renderAjax(Request $request, $data){
        if ($request->isXmlHttpRequest()){

            $serializer = new Serializer(array(), array(
                'json' => new \Symfony\Component\Serializer\Encoder\JsonEncoder(),
                'xml' => new \Symfony\Component\Serializer\Encoder\XmlEncoder()
            ));

            $params = json_decode($this->get("request")->getContent(), true);

            return new Response($serializer->encode($data, $params['_format']));
        }else{
            throw new AccessDeniedHttpException();
            
        }
    }

}
