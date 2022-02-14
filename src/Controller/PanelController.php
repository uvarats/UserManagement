<?php

namespace App\Controller;


use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\Json;

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
    #[Route('panel/delete/{ids}', name: 'delete_user')]
    public function deleteUsers(string $ids, ManagerRegistry $doctrine, TokenStorageInterface $token) :Response{
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /**
         * @var User $currentUser
         */
        $idArray = array_map('intval', explode(',', $ids));
        $currentUser = $this->getUser();
        $currentUserId = $currentUser->getId();
        $selfDeleteFlag = false;
        $entityManager = $doctrine->getManager();
        foreach($idArray as $id){
            if($currentUserId == $id){
                $selfDeleteFlag = true;
                continue;
            }
            $this->deleteUser($id, $entityManager);
        }
        if($selfDeleteFlag === true){
            $this->deleteUser($currentUserId, $entityManager);
            $token->setToken(null);
        }

        return new Response();
    }
    #[Route('/panel/switch/{ids}', name: 'switch_users')]
    public function switchUsersStatus(string $ids, ManagerRegistry $doctrine){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        /**
         * @var User $currentUser
         */
        $currentUser = $this->getUser();
        $idArray = array_map('intval', explode(',', $ids));
        $selfSwitchFlag = false;
        $newStatuses = [];
        foreach($idArray as $id){
            if($currentUser->getId() == $id){
                $selfSwitchFlag = true;
                continue;
            }
            $newStatuses[] = $this->switchUser($id, $doctrine->getManager());
        }
        if($selfSwitchFlag === true){
            $newStatuses[] = $this->switchUser($currentUser->getId(), $doctrine->getManager());

        }
        return new JsonResponse($newStatuses);
    }
    public function deleteUser(int $id, $entityManager){
        $user = $entityManager->getRepository(User::class)->find($id);

        $entityManager->remove($user);
        $entityManager->flush();
    }
    public function switchUser(int $id, $entityManager){
        $user = $entityManager->getRepository(User::class)->find($id);
        $user->setStatus($user->getStatus() === 'AVAILABLE' ? 'LOCKED' : 'AVAILABLE');
        $entityManager->flush();
        return json_encode(['id' => $user->getId(), 'new_status' => ucwords(strtolower($user->getStatus()))], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
    }
}
