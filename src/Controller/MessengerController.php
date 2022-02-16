<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MessengerController extends AbstractController
{
    #[Route('/messenger', name: 'messenger_main')]
    public function main(){

    }
}