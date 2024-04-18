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
use UserBundle\Entity\Photo;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserController extends Controller
{
    /**
     * @Route("/user/me")
     */
    public function meAction(Request $request)
    {
        if($request->isXmlHttpRequest()){
            $me = $this->getUser();
            if(!$me || is_null($me))
                throw new NotFoundHttpException();
            return $this->renderAjax($request, $me->toArray());
        }else{
            throw new  AccessDeniedHttpException();
        }
    }

    /**
     * @Route("/user/get")
     */
    public function getAction(Request $request)
    {

        if($request->isXmlHttpRequest()){

            $params = json_decode($this->get("request")->getContent(), true);

            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository('UserBundle:User');
            try {
                $uid = $params['uid'];
                $user = $repository->find($uid);
                if(is_null($user))
                    throw new  NotFoundHttpException();
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
            return $this->renderAjax($request, $user->toArray());
        }else{
            throw new  AccessDeniedHttpException();
        }
    }

    /**
     * @Route("/user/modif")
     */
    public function modifAction()
    {
        return $this->render('ApiBundle:User:modif.html.twig', array(
            // ...
        ));
    }


    /**
     * @Route("/user/upload")
     */
    public function uploadAction(Request $request)
    {
        $uploaddir = realpath("/web/")."uploads/".$this->getUser()->getId();

        if (!file_exists($uploaddir))
            mkdir($uploaddir, 0777, true);

        $uploadfile = $uploaddir ."/".$_FILES['file']['name'];

        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)){

            try{
                // mise a jour des données
                list($width, $height, $type, $attr) = getimagesize($uploadfile);

                $em = $this->getDoctrine()->getManager();
                $photo = $em->getRepository("UserBundle:Photo")->findOneBy(array("profile"=>$this->getUser()->getProfile()));
                if(!$photo)
                    $photo = new Photo();
                // $photo->setUpdatedAt(new \DateTime());
                $photo->setSrc("/".$uploadfile)->setWidth($width)->setHeight($height)
                    ->setProfile($this->getUser()->getProfile());
                

                $em = $this->getDoctrine()->getManager();
                $em->persist($photo);

                $profile = $this->getUser()->getProfile()->setPhoto($photo);
                // $user = $this->getUser()->getProfile()->setUpdatedAt(new \DateTime());

                $em->persist($profile);
                // $em->persist($user);

                $em->flush();
            }catch(Exception $e){
                throw new BadRequestHttpException($e->getMessage());
            }
                


            return $this->renderAjaxJson($request, array("error"=>false, "file"=>$uploadfile, "dir"=>$uploaddir));
        }
        else
            return $this->renderAjaxJson($request, array("error"=>true));
        
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


    private function renderAjaxJson(Request $request, $data){
        if ($request->isXmlHttpRequest()){

            $serializer = new Serializer(array(), array(
                'json' => new \Symfony\Component\Serializer\Encoder\JsonEncoder(),
                'xml' => new \Symfony\Component\Serializer\Encoder\XmlEncoder()
            ));

            return new Response($serializer->encode($data, 'json'));
        }else{
            throw new AccessDeniedHttpException();
            
        }
    }

}
