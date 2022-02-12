<?php

namespace App\Controller;


use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanelController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function redirectToIndex(){
        return $this->redirectToRoute(route: 'panel');
    }
    #[Route('/panel', name: 'panel')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->findBy([
            'username' => $this->getUser()->getUserIdentifier(),
        ]);
        $user[0]->setLastVisit(new \DateTime());
        $entityManager->flush();
        $users = $entityManager->getRepository(User::class)->findAll();
        return $this->render('panel/index.html.twig', [
            'users' => $users,
        ]);
    }
    #[Route('/panel/switch/{id}', name: 'switch_user')]
    public function switchUserStatus(int $id, ManagerRegistry $doctrine){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $doctrine->getRepository(User::class)->find($id);
        $user->setStatus($user->getStatus() === 'AVAILABLE' ? 'LOCKED' : 'AVAILABLE');
        $doctrine->getManager()->flush();
        return new JsonResponse(['username' => $user->getUsername(), 'new_status' => $user->getStatus()]);
    }

}
