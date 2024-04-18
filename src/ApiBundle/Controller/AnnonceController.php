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

class AnnonceController extends Controller
{
    /**
     * @Route("/annonce/get")
     */
    public function getAction(Request $req)
    {
        if($req->isXmlHttpRequest()){
            $params = json_decode($this->get("request")->getContent(), true);

            $userParams = $params['user'];
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            switch ($params['type']) {
                case 'Demenagement':
                    $repository = $em->getRepository('AnnonceBundle:Demenagement');
                    $data = $repository->find($params['id'])->toArray();
                    break;

                case 'Covoiturage':
                    $repository = $em->getRepository('AnnonceBundle:Covoiturage');
                    $data = $repository->find($params['id'])->toArray();
                    break;

                case 'Vtc':
                    $repository = $em->getRepository('AnnonceBundle:vtc');
                    $data = $repository->find($params['id'])->toArray();
                    break;

                case 'Taxi':
                    $repository = $em->getRepository('AnnonceBundle:Taxi');
                    $data = $repository->find($params['id'])->toArray();
                    break;

                case 'Annonce':
                    $repository = $em->getRepository('AnnonceBundle:Annonce');
                    $data = $repository->find($params['id'])->toArray();
                    break;

                case 'Reservation':
                    $repository = $em->getRepository('UserBundle:Reservation');
                    $data = $repository->find($params['id'])->toArray();
                    break;
                
                default:
                    $data = array('error'=>'no type');
                    break;
            }
            
            return $this->renderAjax($req, $data);
        }else{
            throw new BadRequestHttpException();
            
        }
        
    }





    /**
     * @Route("/annonce/getDevis")
     */
    public function getDevisAction(Request $req)
    {
        if($req->isXmlHttpRequest()){
            $params = json_decode($this->get("request")->getContent(), true);

            $userParams = $params['user'];
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $devis = $em->getRepository("UserBundle:devis")->find($params['id']);
            if(!$devis || is_null($devis))
                throw new NotFoundHttpException();
            
            return $this->renderAjax($req, $devis->toArray());



        }else{
            throw new BadRequestHttpException();
        }

    }





    /**
     * @Route("/annonce/disponnible")
     */
    public function disponnibleAction(Request $request){
        if ($request->isXmlHttpRequest()){
            
            $params = json_decode($this->get("request")->getContent(), true);

            $userParams = $params['user'];
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            switch ($params['type']) {
                case 'Vtc':
                    if(!$params['disponnible']['heureDepart'] || is_null($params['disponnible']['heureDepart']))
                        $data = array("value"=>true);
                    else{
                        $repository = $em->getRepository('AnnonceBundle:vtc');
                        $datetime = new \DateTime($params['disponnible']['heureDepart']);
                        $value = $repository->isDisponnible($params['id'], $datetime);
                        $isDay = $repository->isDay($datetime);
                        $data = array("value"=>$value, "isDay"=>$isDay);
                    }
                    break;


                case 'Taxi':
                    if(!$params['disponnible']['heureDepart'] || is_null($params['disponnible']['heureDepart']))
                        $data = array("value"=>true);
                    else{
                        $repository = $em->getRepository('AnnonceBundle:taxi');
                        $datetime = new \DateTime($params['disponnible']['heureDepart']);
                        $value = $repository->isDisponnible($params['id'], $datetime);
                        $isDay = $repository->isDay($datetime);
                        $data = array("value"=>$value, "isDay"=>$isDay);
                    }
                    break;

                default:
                    $data = array('error'=>'no type');
                    break;
            }

            return $this->renderAjax($request, $data);

        }else
            throw new AccessDeniedHttpException();

    }



    /**
     * @Route("/annonce/reservation")
     */
    public function reservationAction(Request $request)
    {
        if ($request->isXmlHttpRequest()){
            $params = json_decode($this->get("request")->getContent(), true);
            $annonce = $params['annonce'];
            $reservationId = $params['reservationId'];

            try{
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository("UserBundle:Reservation");
                $reservation = $repository->findBy(array("id"=>$reservationId, "annonceId"=>$annonce['Id']));
                if(!$reservation || is_null($reservation))
                    throw new NotFoundHttpException();

                return $this->renderAjax($request, $reservation->toArray());
                    
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
