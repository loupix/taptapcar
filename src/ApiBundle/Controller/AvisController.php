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
use UserBundle\Entity\Avis;
use AnnonceBundle\Entity\Taxi;
use AnnonceBundle\Entity\Covoiturage;
use AnnonceBundle\Entity\Demenagement;
use AnnonceBundle\Entity\vtc;


use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AvisController extends Controller
{
    /**
     * @Route("/avis/get")
     */
    public function getAction(Request $request)
    {
        return $this->render('ApiBundle:Avis:get.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/avis/add")
     */
    public function addAction(Request $request)
    {
        if($request->isXmlHttpRequest()){
            $params = json_decode($this->get("request")->getContent(), true);
            try{
                $userManager = $this->container->get('fos_user.user_manager');
                $userTo = $userManager->findUserBy(array('id'=>$params['userId']));

                if(!in_array('note', array_keys($params)) || $params['note'] == null)
                    $params['note'] = 0;

                $avis = new Avis();
                $avis->setClient($userTo)->setAuteur($this->getUser())
                    ->setMessage($params['message'])->setNote($params['note']);

                $em = $this->getDoctrine()->getManager();
                $em->persist($avis);
                $em->flush();

                return $this->renderAjax($request, $avis->toArray());

            }catch(Exception $e){
                throw new Exception($e->getMessage());
            }


        }else
            throw new AccessDeniedHttpException();
    }

    /**
     * @Route("/avis/del")
     */
    public function delAction(Request $request)
    {
        return $this->render('ApiBundle:Avis:del.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/avis/modif")
     */
    public function modifAction(Request $request)
    {
        return $this->render('ApiBundle:Avis:modif.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/avis/getMe")
     */
    public function getMeAction(Request $request)
    {
        return $this->render('ApiBundle:Avis:get_me.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/avis/getAll")
     */
    public function getAllAction(Request $request)
    {
        
        if($request->isXmlHttpRequest()){
            $params = json_decode($this->get("request")->getContent(), true);
            try{
                $userManager = $this->container->get('fos_user.user_manager');
                $userTo = $userManager->findUserBy(array('id'=>$params['userId']));

                $em = $this->getDoctrine()->getManager();


                $allAvis = $em->getRepository("UserBundle:Avis")->findBy(array("client"=>$userTo));
                $allAvis = array_filter($allAvis, function($a){return $a !== null && $a->getAuteur() !== null && $a->getClient() !== null;});
                $avis = array_map(function($a){return $a->toArray();}, $allAvis);
                return $this->renderAjax($request, $avis);

            }catch(Exception $e){
                throw new Exception($e->getMessage());
            }

        }else
            throw new AccessDeniedHttpException();


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
