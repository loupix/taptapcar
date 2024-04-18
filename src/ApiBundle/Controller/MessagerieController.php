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
use MessageBundle\Entity\Message;
use MessageBundle\Entity\MessageMetadata;
use MessageBundle\Entity\Thread;
use MessageBundle\Entity\ThreadMetadata;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


class MessagerieController extends Controller
{
    /**
     * @Route("/messagerie/addFromAnnonce")
     */
    public function addAction(Request $request)
    {
        if($request->isXmlHttpRequest()){

            $params = json_decode($this->get("request")->getContent(), true);
            $annonce = $params['annonce']['Annonce'];

            $userManager = $this->container->get('fos_user.user_manager');

            try{
                $userTo = $userManager->findUserBy(array('id'=>$annonce['Auteur']['Id']));
                $messageTxt = $params['message']['text'];
                $threadId = $params['message']['thread'];
            }catch(Exception $e){
                throw new Exception($e->getMessage());
            }


            $composer = $this->container->get('fos_message.composer');
            if($threadId){
                $provider = $this->container->get("fos_message.provider");
                $thread = $provider->getThread($threadId);
                $messageObj = $composer->reply($thread);
            }else{
                $messageObj = $composer->newThread()
                    ->addRecipient($userTo)
                    ->setSubject($annonce['Id']);
            }
                $message = $messageObj->setSender($this->getUser())
                ->setBody($messageTxt)
                ->getMessage();

            try{
                $sender = $this->container->get('fos_message.sender');
                $sender->send($message);
                return $this->renderAjax($request, $message->toArray());
            }catch(Exception $e){
                throw new Exception($e->getMessage());
                
            }

            

        }else
            throw new AccessDeniedHttpException();
    }

    /**
     * @Route("/messagerie/get")
     */
    public function getAction(Request $request)
    {
        if($request->isXmlHttpRequest()){

            $me = $this->getUser();
            if(!$me || is_null($me))
                throw new AccessDeniedHttpException();


            $params = json_decode($this->get("request")->getContent(), true);
            $provider = $this->container->get('fos_message.provider');

            switch ($params['type']) {
                case 'new':
                    
                    break;

                case 'all':
                    $threads = $provider->getInboxThreads();

                    break;

                case 'sended':
                    $threads = $provider->getSentThreads();
                    break;

                case 'deleted':
                    
                    break;


                case 'annonce':
                    $annonce = $params['annonce']['Annonce'];
                    $threads = $provider->getSentThreads();
                    $threadObj=false;
                    foreach ($threads as $thread) {
                        if($thread->getSubject()==$annonce['Id']){
                            $threadObj = $thread;
                            break;
                        }
                    }

                    if($threadObj){
                        $messages = array_map(function($m){return $m->toArray();}, $threadObj->getMessages()->toArray());
                        return $this->renderAjax($request, $messages);
                    }else{
                        return $this->renderAjax($request, array());
                    }
                    break;
                
                default:
                    throw new NotFoundHttpException();
                    break;
            }


            try{
                $me = $this->getUser();
                if(!$me || is_null($me))
                    throw new NotFoundHttpException();

                switch (variable) {
                    case 'value':
                        # code...
                        break;
                    
                    default:
                        # code...
                        break;
                }

            }catch(Exception $e){
                throw new Exception($e->getMessage());
            }

            return $this->render('ApiBundle:Messagerie:get.html.php', array(
                // ...
            ));
        }else{
            throw new  AccessDeniedHttpException();
        }
    }


    /**
     * @Route("/messagerie/me")
     */
    public function meAction(Request $request)
    {
        return $this->render('ApiBundle:Messagerie:me.html.php', array(
            // ...
        ));
    }

    /**
     * @Route("/messagerie/del")
     */
    public function delAction(Request $request)
    {
        return $this->render('ApiBundle:Messagerie:del.html.php', array(
            // ...
        ));
    }


    /**
     * @Route("/messagerie/delFromAnnonce")
     */
    public function delFromAnnonceAction(Request $request)
    {
        if($request->isXmlHttpRequest()){

            $me = $this->getUser();
            if(!$me || is_null($me))
                throw new AccessDeniedHttpException();
            
            try{
                $params = json_decode($this->get("request")->getContent(), true);
                $provider = $this->container->get('fos_message.provider');

                $thread = $provider->getThread($params['message']['thread']);
                $messages = $thread->getMessages();
                foreach($messages as $message){
                    if($message->getId()==$params['messageId']){
                        $manager = $this->container->get('fos_message.message_manager');
                        $manager->deleteMessage($message);
                        return $this->renderAjax($request, array("error"=>false));
                    }

                }

                // $this->container->get('fos_message.thread_manager')
                //     ->deleteThread($thread);
            }catch(Exception $e){
                throw new Exception($e->getMessage());
            }

            


        }else{
            throw new  AccessDeniedHttpException();
        }
    }

    /**
     * @Route("/messagerie/getAll")
     */
    public function getAllAction(Request $request)
    {
        return $this->render('ApiBundle:Messagerie:get_all.html.php', array(
            // ...
        ));
    }

    /**
     * @Route("/messagerie/modif")
     */
    public function modifAction(Request $request)
    {
        return $this->render('ApiBundle:Messagerie:modif.html.php', array(
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
