<?php

namespace App\Controller;

use App\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class MessengerController extends AbstractController
{
//    #[Route('/messenger', name: 'messenger_main')]
//    public function main(){
//        $this->denyAccessUnlessGranted('ROLE_USER');
//        return new Response("test");
//    }
//    #[Route('/send')]
//    public function send(MessageBusInterface $bus){
//        $bus->dispatch(new Message('Test', 30, 40, 'ggwp'));
//        return new Response("Sended!");
//    }
}