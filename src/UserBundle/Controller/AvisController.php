<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AvisController extends Controller
{
    /**
     * @Route("/avis/get")
     */
    public function getAction()
    {
        return $this->render('UserBundle:Avis:get.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/avis/del")
     */
    public function delAction()
    {
        return $this->render('UserBundle:Avis:del.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/avis/accepte")
     */
    public function accepteAction()
    {
        return $this->render('UserBundle:Avis:accepte.html.twig', array(
            // ...
        ));
    }

}
