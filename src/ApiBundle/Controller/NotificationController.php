<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\Response;

use UserBundle\Entity\User;
use AnnonceBundle\Entity\Taxi;
use AnnonceBundle\Entity\Covoiturage;
use AnnonceBundle\Entity\Demenagement;
use AnnonceBundle\Entity\vtc;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class NotificationController extends Controller
{

    /**
     * @Route("/notification/get")
     */
    public function getAction()
    {
        return $this->render('ApiBundle:Notification:get.html.twig', array(
            // ...
        ));
    }

    
    /**
     * @Route("/notification/add")
     */
    public function addAction(Request $request)
    {
        
    }

    /**
     * @Route("/notification/del")
     */
    public function delAction(Request $request){

        $this->container->get("Notification")->del($notifId);
    }

    /**
     * @Route("/notification/modif")
     */
    public function modifAction()
    {
        return $this->render('ApiBundle:Notification:modif.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/notification/vue")
     */
    public function vueAction()
    {
        return $this->render('ApiBundle:Notification:vue.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/notification/getAll")
     */
    public function getAllAction(Request $request)
    {

        $user = $this->getUser();
        if(!$user || is_null($user))
            throw new BadRequestHttpException("Pas d'user");

        try{
            $notifs = $this->container->get("notification")->getNewUnread($user);
        }catch(Exception $e){
            return $this->renderAjax($request, array("error"=>$e->getMessage()));
        }

        $data = array_map(function($o){
            return $o->toArray();}, $notifs);


        return $this->renderAjax($request, $data);
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
