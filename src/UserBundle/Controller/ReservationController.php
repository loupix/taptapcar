<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException ;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;


use UserBundle\Entity\Reservation;
use UserBundle\Entity\Devis;

class ReservationController extends Controller
{

    /**
     * @Route("/reservation/{reservationId}")
     */
    public function getReservationAction(Request $request)
    {   
        $reservationId = $request->get('reservationId');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("UserBundle:Reservation");
        $reservation = $repository->find($reservationId);

        if(!$reservation || is_null($reservation))
            throw new NotFoundHttpException();

        return $this->render('UserBundle:Reservation:getReservation.html.twig', array(
            "reservation"=>$reservation
        ));
    }


    



    /**
     * @Route("/reservation/{reservationId}/pdf")
     */
    public function pdfReservationAction(Request $request)
    {   

        $reservationId = $request->get('reservationId');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("UserBundle:Reservation");
        $reservation = $repository->find($reservationId);

        if(!$reservation || is_null($reservation))
            throw new NotFoundHttpException();


        try{

            $template = $this->renderView("UserBundle:Reservation:reservationPDF.html.twig", array(
                "reservation"=>$reservation
            ));

            $pdf = $this->get('html2pdf_factory')->create();
            $pdf->pdf->SetAuthor("Formidrive.com");
            $pdf->pdf->SetTitle('Réservation N° '.$reservation->getNumero());
            $pdf->pdf->SetSubject('Réservation de '.$reservation->getannonce()->getType());
            $pdf->writeHTML($template);
            return $pdf->Output('Devis.pdf',' D');
        }catch (HTML2PDF_exception $e) {
            die($e);
        }
        



    }



    /**
     * @Route("/devis/{devisId}")
     */
    public function getDevisAction(Request $request)
    {   
        $devisId = $request->get('devisId');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("UserBundle:Devis");
        $devis = $repository->find($devisId);

        if(!$devis || is_null($devis))
            throw new NotFoundHttpException();

        return $this->render('UserBundle:Reservation:getDevis.html.twig', array(
            "devis"=>$devis
        ));
    }






    /**
     * @Route("/devis/{devisId}/pdf")
     */
    public function pdfDevisAction(Request $request)
    {   

        $devisId = $request->get('devisId');

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("UserBundle:Devis");
        $devis = $repository->find($devisId);

        if(!$devis || is_null($devis))
            throw new NotFoundHttpException();


        try{

            $template = $this->renderView("UserBundle:Reservation:devisPDF.html.twig", array(
                "devis"=>$devis
            ));

            $pdf = $this->get('html2pdf_factory')->create();
            $pdf->pdf->SetAuthor("Formidrive.com");
            $pdf->pdf->SetTitle('Devis N° '.$devis->getId());
            $pdf->pdf->SetSubject('Devis de '.$devis->getannonce()->getType());
            $pdf->writeHTML($template);
            return $pdf->Output('Devis.pdf',' D');
        }catch (HTML2PDF_exception $e) {
            die($e);
        }




    }





}
