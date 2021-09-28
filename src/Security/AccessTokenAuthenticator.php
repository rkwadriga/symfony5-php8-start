<?php

namespace App\Security;

use App\Entity\User;
use App\Helpers\JsonRequestHelper;
use App\Helpers\JsonResponseHelper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;

class AccessTokenAuthenticator extends AbstractAuthenticator
{
    public const LOGIN_ROUTE = 'security_login';

    private static array $allowedRoutes = [
        self::LOGIN_ROUTE,
    ];
    public function __construct(
        private JsonRequestHelper $jsonRequestHelper,
        private JsonResponseHelper $jsonResponseHelper
    ) {}

    public function supports(Request $request): ?bool
    {
        return in_array($request->attributes->get('_route'), self::$allowedRoutes);
    }

    public function authenticate(Request $request): PassportInterface
    {
        $this->jsonRequestHelper->prepareRequest($request);
        $username = $request->get('username');
        $password = $request->get('password');

        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        /** @var User $user */
        $user = $token->getUser();
        return new JsonResponse(['id' => $user->getId(), 'email' => $user->getEmail(), 'name' => $user->getName()]);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return $this->jsonResponseHelper->createErrorException($exception);
    }

//    public function start(Request $request, AuthenticationException $authException = null): Response
//    {
//        /*
//         * If you would like this class to control what happens when an anonymous user accesses a
//         * protected page (e.g. redirect to /login), uncomment this method and make this class
//         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntrypointInterface.
//         *
//         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
//         */
//    }
}
