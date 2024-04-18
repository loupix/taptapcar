<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AnnonceBundle\Entity\Annonce;
use GeographieBundle\Entity\Place;
use GeographieBundle\Entity\Ville;
use JMS\Serializer\SerializerBuilder;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {        
        $annonce = new Annonce();
        $form = $this->createForm('AnnonceBundle\Form\AnnonceType', $annonce);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // a revoir
            $em = $this->getDoctrine()->getManager();
            $session = $request->getSession();
            
            $data = $form->getData();
            $session->set("dataForm", $data);
            try{
                $typeForm = array('demenagement','vtc','taxi','covoiturage');
                $type = $typeForm[$data->getCategorie()];

                if(in_array($type, array('covoiturage'))){
                    $place = $data->getPlace();


                    $repository = $em->getRepository('GeographieBundle:Ville');


                    $defaultFrom = $place['from']->getLocality() == "" ? true : false;
                    $defaultTo = $place['to']->getLocality() == "" ? true : false;

                    if($place['from']->getLocality() == ""){
                        $place['from']->setLocality("strasbourg");
                    }

                    if($place['to']->getLocality() == ""){
                        $place['to']->setLocality("paris");
                    }


                    $villeFrom = $repository->findByName($place['from']->getLocality());
                    $villeTo = $repository->findByName($place['to']->getLocality());



                    // if(count($villeFrom)==0)
                    //     $villeFrom = $repository->findByCodePostal($place['from']['postal_code']);

                    // if(count($villeTo)==0)
                    //     $villeTo = $repository->findByCodePostal($place['to']['postal_code']);


                    // print_r(array_map(function($v){return $v->getSlug();}, $villeFrom));
                    // die();

                    if(count($villeFrom)==0 || count($villeTo)==0){
                        $this->container->get('session')->getFlashBag()->set("error", "ville non trouvé");
                        return $this->redirect($this->generateUrl('homepage'));
                    }



                    if($defaultTo){
                        $place['to'] = $villeTo[0];
                        // $place['to']['nom'] = $villeTo[0]->getLocality();
                    };
                    
                    
                    if($defaultFrom){
                        $place['from'] = $villeFrom[0];
                        // $place['from']['nom'] = $villeFrom[0]->getLocality();
                    };

                    // print_r($villeFrom[0]->toArray());
                    // die();

                    
                    // $serializer = SerializerBuilder::create()->build();
                    // $array = $serializer->toArray($villeTo[0]);
                    // print_r($array);
                    // die();

                    $annonce->setPlace($place);
                    $newForm = $this->createForm('AnnonceBundle\Form\AnnonceType', $annonce);
                    $newForm->setData($form->getData());


                    // $form = $form->setData(
                    //     Array('place' => Array('from' => $villeFrom[0], 'to' => $villeTo[0]))
                    // );

                    $session->set("dataForm", $newForm->getData());


                    
                    $session->set("typeRecherche", $type);
                    $session->set("villeFrom", $villeFrom[0]);
                    $session->set("villeTo", $villeTo[0]);
                    $session->set("place", $place);
                    
                    return $this->redirect($this->generateUrl('annonce_annonce_find'));

                }else if(in_array($type, array('demenagement', 'vtc', 'taxi'))){
                    $rayon = $data->getRayon();

                    $session = $request->getSession();
                    $session->set("typeRecherche", $type);
                    $session->set("rayon", $rayon);

                    return $this->redirect($this->generateUrl('annonce_annonce_find'));
                }

            }catch(Exception $e){
                die($e->getMessage());
            }
            
            print_r($type);
            die();

        }else{
            $em = $this->getDoctrine()->getManager();
            $lastAnnonces = $em->getRepository("AnnonceBundle:Annonce")->findBy(array(), array("createdAt" => "DESC"), 5);
        }
        


        return $this->render('default/index.html.twig', array(
            'lastAnnonces'=>$lastAnnonces,
            'formRecherche'=>$form->createView(),
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ));
    }
}
