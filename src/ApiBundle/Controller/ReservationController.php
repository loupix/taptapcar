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

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


class ReservationController extends Controller
{
    /**
     * @Route("/reservation/get")
     */
    public function getAction(Request $request)
    {
        if ($request->isXmlHttpRequest()){
            $params = json_decode($this->get("request")->getContent(), true);
            $reservationId = $params['reservationId'];

            try{
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository("UserBundle:Reservation");
                $reservation = $repository->findBy(array("id"=>$reservationId));
                if(!$reservation || is_null($reservation) || count($reservation)==0)
                    throw new NotFoundHttpException();

                $reservation = $reservation[0];

                return $this->renderAjax($request, $reservation->toArray());
                    
            }catch(Exception $e){
                throw new Exception($e->getMessage());
            }
            

        }else
            throw new AccessDeniedHttpException();
    }



    /**
     * @Route("/reservation/valider")
     */
    public function validAction(Request $request)
    {
        if ($request->isXmlHttpRequest()){
            $params = json_decode($this->get("request")->getContent(), true);
            $reservationId = $params['reservationId'];
            $valider = $params['valider'];

            try{
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository("UserBundle:Reservation");
                $reservation = $repository->findBy(array("id"=>$reservationId));
                if(!$reservation || is_null($reservation) || count($reservation)==0)
                    throw new NotFoundHttpException();

                $reservation = $reservation[0];

                $reservation->setValider($valider)->setEnCours(false);

                $em->persist($reservation);
                $em->flush();





                // envoie notification

                try{
                    $text = "Votre réservation a été ".($reservation->getValider() ? "accepter" : "refuser");
                    $this->container->get("Notification")
                        ->add($reservation->getAnnonce()->getAuteur(), $reservation->getAnnonce(), $text, "reservation", $reservation);                    
                }catch(Exception $e){
                    throw $e;
                }
                


                // envoie SMS
                try{
                    
                    if(!is_null($reservation->getAnnonce()->getAuteur()->getTelephone()))
                        $this->container->get("Smsbox")->sendReservation($reservation);
                }catch(\Exception $e){
                    throw $e;
                }

                // Envoie d'un message
                try{
                    if($reservation->getValider())
                        $this->container->get('sendEmail')->setUser($reservation->getAnnonce()->getAuteur())->setReservation($reservation)->reservationValide();
                    else
                        $this->container->get('sendEmail')->setUser($reservation->getAnnonce()->getAuteur())->setReservation($reservation)->reservationAnnuler();
                }catch(Exception $e){
                    throw $e;
                }




                return $this->renderAjax($request, $reservation->toArray());
                    
            }catch(Exception $e){
                throw new Exception($e->getMessage());
            }
            

        }else
            throw new AccessDeniedHttpException();
    }






    /**
     * @Route("/reservation/getAll")
     */
    public function getAllAction()
    {
        return $this->render('ApiBundle:Reservation:get_all.html.twig', array(
            // ...
        ));
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

}
 