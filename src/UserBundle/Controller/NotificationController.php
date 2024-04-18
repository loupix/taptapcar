<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class NotificationController extends Controller
{
    /**
     * @Route("/notification/get")
     */
    public function getAction()
    {
        return $this->render('UserBundle:Notification:get.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/notification/count")
     */
    public function countAction()
    {
        return $this->render('UserBundle:Notification:count.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/notification/del")
     */
    public function delAction()
    {
        return $this->render('UserBundle:Notification:del.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/notification/read")
     */
    public function readAction()
    {
        return $this->render('UserBundle:Notification:read.html.twig', array(
            // ...
        ));
    }

}
