<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MessengerController extends AbstractController
{
    #[Route('/messenger', name: 'messenger_main')]
    public function main(ManagerRegistry $doctrine){
        $this->denyAccessUnlessGranted('ROLE_USER');
        $entityManager = $doctrine->getManager();
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $messages = $entityManager->getRepository(Message::class)->findBy(['recipientId' => $user->getId()]);
        $messagesResponse = "";
        foreach ($messages as $message){
            $sender = $entityManager->getRepository(User::class)->find($message->getSenderId());
            $messagesResponse .= "Subject: {$message->getSubject()}<br>
            From (username): {$sender->getUsername()}<br>Text: {$message->getText()}<br>
            Sended: {$message->getSendDate()->format('Y-m-d H:i:s')}<br><br>";
        }
        return new Response($messagesResponse);
    }
    #[Route('/message/{id}')]
    public function getMessage(int $id, ManagerRegistry $doctrine){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $entityManager = $doctrine->getManager();
        $message = $entityManager->getRepository(Message::class)->find($id);
        if($message->getRecipientId() == $user->getId()){
            return new JsonResponse([
                'subject' => $message->getSubject(),
                'sender' => $message->getSenderId(),
                'text' => $message->getText(),
                'date' => $message->getSendDate()
            ]);
        }
        return new Response();
    }
    #[Route('/send')]
    public function send(MessageBusInterface $bus, ManagerRegistry $doctrine){
        $entityManager = $doctrine->getManager();

        $message = new Message();
        $message->setSubject('Vadim')
            ->setSenderId(53)
            ->setRecipientId(51)
            ->setText('Вадим даник. Качеля лох.')
            ->setSendDate(new \DateTime())
            ->setIsNew(true)
            ->setIsRead(false);

        $bus->dispatch($message);
        $entityManager->persist($message);
        $entityManager->flush();
        return new Response("Sended!");
    }
}