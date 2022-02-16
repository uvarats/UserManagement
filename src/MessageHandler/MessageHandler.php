<?php

namespace App\MessageHandler;

use App\Entity\Message;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class MessageHandler implements MessageHandlerInterface
{
    public function __invoke(Message $message)
    {

    }
}
