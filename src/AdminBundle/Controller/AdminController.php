<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AdminController extends Controller
{

    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->redirect($this->generateUrl("admin_admin_users"));
    }


    /**
     * @Route("/users")
     */
    public function usersAction()
    {
        return $this->render('AdminBundle:Admin:users.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/annonces")
     */
    public function annoncesAction()
    {
        return $this->render('AdminBundle:Admin:annonces.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/reservations")
     */
    public function reservationsAction()
    {
        return $this->render('AdminBundle:Admin:reservations.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/paiements")
     */
    public function paiementsAction()
    {
        return $this->render('AdminBundle:Admin:paiements.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/contacts")
     */
    public function contactsAction()
    {
        return $this->render('AdminBundle:Admin:contacts.html.twig', array(
            // ...
        ));
    }


    /**
     * @Route("/fees")
     */
    public function feesAction()
    {
        return $this->render('AdminBundle:Admin:fees.html.twig', array(
            // ...
        ));
    }


    /**
     * @Route("/fees/distribution")
     */
    public function distributionAction()
    {
        $fees = $this->container->get("Paiement")
            ->setUser($this->getUser())
            ->getFees();
    }


    /**
     * @Route("/fees/paiement")
     */
    public function paiementAction()
    {
        $this->container->get("Paiement")->getFees();
    }

    /**
     * @Route("/erreurs")
     */
    public function erreursAction()
    {
        return $this->render('AdminBundle:Admin:erreurs.html.twig', array(
            // ...
        ));
    }

}
