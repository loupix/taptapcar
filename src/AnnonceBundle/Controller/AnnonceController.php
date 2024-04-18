<?php

namespace AnnonceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\Response;

use AnnonceBundle\Entity\Taxi;
use AnnonceBundle\Entity\covoiturage;
use AnnonceBundle\Entity\Demenagement;
use AnnonceBundle\Entity\vtc;


use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AnnonceController extends Controller
{


    /**
     * @Route("/get/{annonceId}")
     */
    public function getAction($annonceId){
        $em = $this->getDoctrine()->getManager();
        $annonce = $em->getRepository("AnnonceBundle:Annonce")->find($annonceId);

        if(!$annonce || is_null($annonce))
            throw new NotFoundHttpException();

        $object = $annonce->getObject();
        if(!$object || is_null($object))
            throw new NotFoundHttpException();

        switch ($annonce->getType()) {
            case 'Vtc':
                return $this->redirect($this->generateUrl("annonce_vtc_get", array("id"=>$object->getId())));
                break;

            case 'Taxi':
                return $this->redirect($this->generateUrl("annonce_taxi_get", array("id"=>$object->getId())));
                break;

            case 'Covoiturage':
                return $this->redirect($this->generateUrl("annonce_covoiturage_get", array("id"=>$object->getId())));
                break;

            case 'Demenagement':
                return $this->redirect($this->generateUrl("annonce_demenagement_get", array("id"=>$object->getId())));
                break;
            
            default:
                # code...
                break;
        }

    }


    /**
     * @Route("/modifier/{annonceId}", name="annonce_modifier")
     */
    public function modifAction($annonceId){
        $em = $this->getDoctrine()->getManager();
        $annonce = $em->getRepository("AnnonceBundle:Annonce")->find($annonceId);

        if(!$annonce || is_null($annonce))
            throw new NotFoundHttpException("Annonce non trouvés.");

        $object = $annonce->getObject();
        if(!$object || is_null($object))
            throw new NotFoundHttpException("L'annonce n'a pas d'objet");

        switch ($annonce->getType()) {
            case 'Vtc':
                return $this->redirect($this->generateUrl("annonce_vtc_modif", array("id"=>$object->getId())));
                break;

            case 'Taxi':
                return $this->redirect($this->generateUrl("annonce_taxi_modif", array("id"=>$object->getId())));
                break;

            case 'Covoiturage':
                return $this->redirect($this->generateUrl("annonce_covoiturage_modif", array("id"=>$object->getId())));
                break;

            case 'Demenagement':
                return $this->redirect($this->generateUrl("annonce_demenagement_modif", array("id"=>$object->getId())));
                break;
            
            default:
                # code...
                break;
        }

    }





    /**
     * @Route("/find")
     */
    public function findAction(Request $request)
    {
        $session = $request->getSession();

        $typeForm = array('demenagement','vtc','taxi','covoiturage');
        $type = $session->get("typeRecherche");
        $dataForm = $session->get("dataForm");


        $formRecherche = $this->createForm('AnnonceBundle\Form\AnnonceType', $dataForm);
        $em = $this->getDoctrine()->getManager();


        if($type=="taxi"){
            $repository = $em->getRepository('AnnonceBundle:Taxi');
            // $liste = $repository->findAll();
            $formOption = $this->createForm('AnnonceBundle\Form\TaxiType', new Taxi());

        }else if($type=="vtc"){
            $repository = $em->getRepository('AnnonceBundle:vtc');
            // $liste = $repository->findByRayon($dataForm->getRayon()['radius'], $this);
            // $liste = $repository->findAll();
            $formOption = $this->createForm('AnnonceBundle\Form\VtcType', new vtc());
        }

        else if($type=="demenagement"){
            $repository = $em->getRepository('AnnonceBundle:Demenagement');
            // $liste = $repository->findByRayon($dataForm['rayon']);
            // $liste = $repository->findAll();
            $formOption = $this->createForm('AnnonceBundle\Form\DemenagementTypeRecherche', new Demenagement());
        }else if($type=="covoiturage"){
            $repository = $em->getRepository('AnnonceBundle:covoiturage');
            // $liste = $repository->findByRayon($dataForm['rayon']);
            // $liste = $repository->findAll();
            $formOption = $this->createForm('AnnonceBundle\Form\CovoiturageTypeUnique', new Covoiturage());
        }else{
            throw new NotFoundHttpException("Pas de type de recherche trouvés");
            
        }


        return $this->render('AnnonceBundle:Annonce:'.$type.'_find.html.twig', array(
            'formRecherche'=>$formRecherche->createView(),
            'dataRecherche'=>$dataForm,
            'formOption'=>$formOption->createView(),
            'pageEncours'=>0,
            'nbPages'=>0
        ));
    }


    /**
     * @Route("/taxiFind")
     */
    public function taxiFindAction()
    {
        if ($request->isXmlHttpRequest()){

            $serializer = new Serializer(array(), array(
                'json' => new \Symfony\Component\Serializer\Encoder\JsonEncoder(),
                'xml' => new \Symfony\Component\Serializer\Encoder\XmlEncoder()
            ));

            $data = array('hello' => 'world');

            return new Response($serializer->encode($data, $this->getRequest()->get('_format')));
        }


        return $this->redirect($this->generateUrl('annonce_annonce_find'));
    }

    /**
     * @Route("/vtcFind")
     */
    public function vtcFindAction()
    {
        if ($request->isXmlHttpRequest()){

            $serializer = new Serializer(array(), array(
                'json' => new \Symfony\Component\Serializer\Encoder\JsonEncoder(),
                'xml' => new \Symfony\Component\Serializer\Encoder\XmlEncoder()
            ));

            $data = array('hello' => 'world');

            return new Response($serializer->encode($data, $this->getRequest()->get('_format')));
        }


        return $this->redirect($this->generateUrl('annonce_annonce_find'));
    }

    /**
     * @Route("/covoiturageFind")
     */
    public function covoiturageFindAction()
    {
        if ($request->isXmlHttpRequest()){

            $serializer = new Serializer(array(), array(
                'json' => new \Symfony\Component\Serializer\Encoder\JsonEncoder(),
                'xml' => new \Symfony\Component\Serializer\Encoder\XmlEncoder()
            ));

            $data = array('hello' => 'world');

            return new Response($serializer->encode($data, $this->getRequest()->get('_format')));
        }


        return $this->redirect($this->generateUrl('annonce_annonce_find'));
    }

    /**
     * @Route("/demenagementFind")
     */
    public function demenagementFindAction()
    {
        if ($request->isXmlHttpRequest()){

            $serializer = new Serializer(array(), array(
                'json' => new \Symfony\Component\Serializer\Encoder\JsonEncoder(),
                'xml' => new \Symfony\Component\Serializer\Encoder\XmlEncoder()
            ));

            $data = array('hello' => 'world');

            return new Response($serializer->encode($data, $this->getRequest()->get('_format')));
        }


        return $this->redirect($this->generateUrl('annonce_annonce_find'));
    }


}
