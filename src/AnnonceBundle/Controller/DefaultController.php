<?php

namespace AnnonceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('AnnonceBundle:Default:index.html.twig');
    }


    /**
     * @Route("/ajout")
     */
    public function ajoutAction()
    {
        $user = $this->getUser();
        // if(is_null($user) || !$user)
        //     return $this->redirect($this->generateUrl('fos_user_security_login'));
        return $this->render('AnnonceBundle:Default:ajout.html.twig');
    }
}
