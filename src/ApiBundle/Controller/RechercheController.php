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
use AnnonceBundle\Entity\covoiturage;
use AnnonceBundle\Entity\Demenagement;
use AnnonceBundle\Entity\vtc;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RechercheController extends Controller
{
	/**
     * @Route("/recherche")
     */
    public function rechercheAction(Request $req)
    {
        if($req->isXmlHttpRequest()){

        	$params = json_decode($this->get("request")->getContent(), true);

            switch ($params['type']) {
            	case 'Demenagement':
            		$data = $this->Demenagement($params);
            		break;

            	case 'Covoiturage':
            		$data = $this->Covoiturage($params);
            		break;

            	case 'Vtc':
            		$data = $this->Vtc($params);
            		break;

            	case 'Taxi':
            		$data = $this->Taxi($params);
            		break;
            	
            	default:
            		$data = array('error'=>'no type');
            		break;
            }
            try {
            	return $this->renderAjax($req, $data);
            } catch (Exception $e) {
            	throw new HttpException();
            }
            
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


    private function Demenagement($params){
    	$nbResult=$params['_maxSize'];
        $firstResult = $params['_page']*$nbResult;



        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AnnonceBundle:Demenagement');


        if(is_null($params['_position'])){
            $geoip = $this->get('maxmind.geoip')->lookup($this->getIp());
            $lat = $geoip->getLatitude();
            $lon = $geoip->getLongitude();
        }else{
            $lat = $params['_position']['latitude'];
            $lon = $params['_position']['longitude'];
        }
        

        $query = $repository->genQuery($params, $lat, $lon);
        $maxResult = $repository->countAll(clone $query);
        $annonces = $repository->allPaginator(clone $query, $firstResult, $nbResult);

        $annonces = array_map(function($a){return $a->toArray();}, $annonces);


        $data = array(
            "annonces"=>$annonces,
            "maxResult"=>$maxResult
        );
        return $data;

    }



    private function Covoiturage($params){
    	$nbResult=$params['_maxSize'];
        $firstResult = $params['_page']*$nbResult;

        $depart = $params['depart'];
        $arrivee = $params['arrivee'];
        list($d, $m, $y) = explode("/", $params['date']);
        $date = implode("-", array($y, $m, $d));



        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AnnonceBundle:covoiturage');



        $query = $repository->genQuery($params, $depart, $arrivee, $date);
        $maxResult = $repository->countAll(clone $query);
        $annonces = $repository->allPaginator(clone $query, $firstResult, $nbResult);

        $annonces = array_map(function($a){return $a->toArray();}, $annonces);


        $data = array(
            "annonces"=>$annonces,
            "maxResult"=>$maxResult
        );
        return $data;
    }



    private function Taxi($params){
    	$nbResult=$params['_maxSize'];
        $firstResult = $params['_page']*$nbResult;

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AnnonceBundle:Taxi');


        if(is_null($params['_position'])){
            $geoip = $this->get('maxmind.geoip')->lookup($this->getIp());
            $lat = $geoip->getLatitude();
            $lon = $geoip->getLongitude();
        }else{
            $lat = $params['_position']['latitude'];
            $lon = $params['_position']['longitude'];
        }



        $query = $repository->genQuery($params, $lat, $lon);
        $maxResult = $repository->countAll(clone $query);
        $annonces = $repository->allPaginator(clone $query,$firstResult, $nbResult);

        $annonces = array_map(function($a){return $a->toArray();}, $annonces);


        $data = array(
            "annonces"=>$annonces,
            "maxResult"=>$maxResult,
            "position"=>array('latitude'=>$lat, 'longitude'=>$lon)
        );
        return $data;
    }



    private function Vtc($params){
    	$nbResult=$params['_maxSize'];
    	$firstResult = $params['_page']*$nbResult;



        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AnnonceBundle:vtc');

        if(is_null($params['_position'])){
            $geoip = $this->get('maxmind.geoip')->lookup($this->getIp());
            $lat = $geoip->getLatitude();
            $lon = $geoip->getLongitude();
        }else{
            $lat = $params['_position']['latitude'];
            $lon = $params['_position']['longitude'];
        }
        



        $query = $repository->genQuery($params, $lat, $lon);
        $annonces = $repository->allPaginator(clone $query, $firstResult, $nbResult);
        $maxResult = $repository->countAll(clone $query);

        $annonces = array_map(function($a){return $a->toArray();}, $annonces);


        $data = array(
            "annonces"=>$annonces,
            "maxResult"=>$maxResult,
            "position"=>array('latitude'=>$lat, 'longitude'=>$lon)
        );
        return $data;
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
