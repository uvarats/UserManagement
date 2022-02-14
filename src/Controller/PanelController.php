<?php

namespace App\Controller;


use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
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
    #[Route('/panel/check/{id}')]
    public function checkIsCurrent(Request $request, $id){
        /**
         * @var User $current_user
         */
        $current_user = $this->getUser();
        $flag = $current_user != null && $current_user->getId() == $id;
        return new JsonResponse(['is_current' => $flag]);
    }
    #[Route('panel/delete/{id}', name: 'delete_user', methods: 'POST')]
    public function deleteUser(Request $request, int $id, ManagerRegistry $doctrine) :Response{
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /**
         * @var User $currentUser
         */
        $currentUser = $this->getUser();

        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);
        $currentUserId = $currentUser->getId();

        if($currentUserId != $id){
            $entityManager->remove($user);
            $entityManager->flush();
            return new JsonResponse(['is_current' => false]);
        }

        return new JsonResponse(['is_current' => true]);
    }


}
