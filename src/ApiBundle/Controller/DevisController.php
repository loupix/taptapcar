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
use UserBundle\Entity\Devis;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class DevisController extends Controller
{
    /**
     * @Route("/devis/get")
     */
    public function getAction(Request $request)
    {
        if ($request->isXmlHttpRequest()){
            $params = json_decode($this->get("request")->getContent(), true);
            $devisId = $params['devisId'];

            try{
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository("UserBundle:Devis");
                $devis = $repository->findBy(array("id"=>$devisId));
                if(!$devis || is_null($devis) || count($devis)==0)
                    throw new NotFoundHttpException();

                $devis = $devis[0];

                return $this->renderAjax($request, $devis->toArray());
                    
            }catch(Exception $e){
                throw new Exception($e->getMessage());
            }
            

        }else
            throw new AccessDeniedHttpException();
    }

    /**
     * @Route("/devis/accepter")
     */
    public function accepterAction(Request $request)
    {
        if ($request->isXmlHttpRequest()){
            $params = json_decode($this->get("request")->getContent(), true);
            $devisId = $params['devisId'];
            $accepter = $params['accepter'];

            try{
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository("UserBundle:Devis");
                $devis = $repository->findBy(array("id"=>$devisId));
                if(!$devis || is_null($devis) || count($devis)==0)
                    throw new NotFoundHttpException();

                $devis = $devis[0];

                $devis->setAccepter($accepter)
                    ->setRefuser(!$accepter)
                    ->setEnCours(false);

                $em->persist($devis);
                $em->flush();




                // envoie notification

                try{
                    $text = "Votre devis a été ".($devis->getAccepter() ? "accepter" : "annuler");
                    $this->container->get("Notification")
                        ->add($devis->getAnnonce()->getAuteur(), $devis->getAnnonce(), $text, "devis", null, $devis);                    
                }catch(Exception $e){
                    throw $e;
                }
                


                // envoie SMS
                try{
                    
                    if(!is_null($devis->getAnnonce()->getAuteur()->getTelephone()))
                        $this->container->get("Smsbox")->sendDevis($devis);
                }catch(\Exception $e){
                    throw $e;
                }

                // Envoie d'un E-Mail
                try{
                    if($devis->getAccepter())
                        $this->container->get('sendEmail')->setUser($devis->getAnnonce()->getAuteur())->setDevis($devis)->devisAccepter();
                    else
                        $this->container->get('sendEmail')->setUser($devis->getAnnonce()->getAuteur())->setDevis($devis)->devisRefusee();
                }catch(Exception $e){
                    throw $e;
                }




                return $this->renderAjax($request, $devis->toArray());
                    
            }catch(Exception $e){
                throw new Exception($e->getMessage());
            }
            

        }else
            throw new AccessDeniedHttpException();
    }

    /**
     * @Route("/devis/refuser")
     */
    public function refuserAction(Request $request)
    {
        if ($request->isXmlHttpRequest()){
            $params = json_decode($this->get("request")->getContent(), true);
            $devisId = $params['devisId'];
            $refuser = $params['refuser'];

            try{
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository("UserBundle:Devis");
                $devis = $repository->findBy(array("id"=>$devisId));
                if(!$devis || is_null($devis) || count($devis)==0)
                    throw new NotFoundHttpException();

                $devis = $devis[0];

                $devis->setRefuser($refuser)
                    ->setAccepter(!$refuser)
                    ->setEnCours(false);

                $em->persist($devis);
                $em->flush();

                return $this->renderAjax($request, $devis->toArray());
                    
            }catch(Exception $e){
                throw new Exception($e->getMessage());
            }
            

        }else
            throw new AccessDeniedHttpException();
    }

    /**
     * @Route("/devis/getAll")
     */
    public function getAllAction(Request $request)
    {
        return $this->render('ApiBundle:Devis:get_all.html.twig', array(
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
