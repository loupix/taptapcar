<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\contact;

class contactController extends Controller
{
    /**
     * @Route("/contact", name="app_contact")
     */
    public function contactAction()
    {
    	$contactForm = $this->createForm('AppBundle\Form\contactType', new contact());

        if ($contactForm->isSubmitted() && $contactForm->isValid()) {

            try{
                $contact = $contactForm->getData();
                $em = $this->getDoctrine()->getManager();

                $em->persist($Vtc);
                $em->flush();

                $this->container->get('session')->getFlashBag()->set("success", "Message envoyé à l'administration");
            }catch(Exception $e){
                $this->container->get('session')->getFlashBag()->set("error", "Message non envoyé <br /><small>".$e->getMessage()."</small>");
            }
        };

        return $this->render('AppBundle:contact:contact.html.twig', array(
            "contactForm"=>$contactForm->createView()
        ));
    }

}
