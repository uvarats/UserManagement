<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class AuthService
{
//    public static AuthorizationChecker $checker;
//    public static TokenStorage $tokenStorage;
    public static ?TokenInterface $token = null;
    public static ?Session $session = null;
    public static ?Passport $passport = null;
}