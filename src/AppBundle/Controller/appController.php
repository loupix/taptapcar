<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class appController extends Controller
{
    /**
     * @Route("/ccm", name="app_ccm")
     */
    public function ccmAction()
    {
        return $this->render('AppBundle:app:ccm.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/assurance", name="app_assurance")
     */
    public function assuranceAction()
    {
        return $this->render('AppBundle:app:assurance.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/annulations", name="app_annulations")
     */
    public function annulationsAction()
    {
        return $this->render('AppBundle:app:annulations.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/faq", name="app_faq")
     */
    public function faqAction()
    {
        return $this->render('AppBundle:app:faq.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/infos", name="app_infos")
     */
    public function infosAction()
    {
        return $this->render('AppBundle:app:infos.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/cookies", name="app_cookies")
     */
    public function cookiesAction()
    {
        return $this->render('AppBundle:app:cookies.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/cgu", name="app_cgu")
     */
    public function cguAction()
    {
        return $this->render('AppBundle:app:cgu.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/charte", name="app_charte")
     */
    public function charteAction()
    {
        return $this->render('AppBundle:app:charte.html.twig', array(
            // ...
        ));
    }

}
