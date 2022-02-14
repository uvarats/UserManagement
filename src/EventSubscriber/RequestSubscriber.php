<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Security\AuthService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;

class RequestSubscriber implements EventSubscriberInterface
{
    private AuthorizationCheckerInterface $authorizationChecker;
    private TokenStorageInterface $tokenStorage;
    private LoggerInterface $logger;
    private Request $request;
    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker, LoggerInterface $logger){
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->logger = $logger;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if(!$event->isMainRequest()) return;
         try {
                if($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')){
                    $token = $this->tokenStorage->getToken();
                    /**
                     * @var User $user
                     */
                    $user = $token->getUser();
                    if($user != null && $user->getStatus() == 'LOCKED'){
                        $this->tokenStorage->setToken(null);

                    }
                }
        } catch(AuthenticationCredentialsNotFoundException $e){

        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }
}
