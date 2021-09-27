<?php

namespace App\Security;

use App\Entity\User;
use App\Helpers\JsonResponseHelper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use App\Helpers\JsonRequestHelper;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'security_login';

    private static array $allowedRoutes = [
        self::LOGIN_ROUTE,
    ];

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private JsonRequestHelper $jsonRequestHelper,
        private JsonResponseHelper $jsonResponseHelper
    ) {}

    public function supports(Request $request): bool
    {
        return in_array($request->attributes->get('_route'), self::$allowedRoutes);
    }

    public function authenticate(Request $request): PassportInterface
    {
        $requestParams = $this->jsonRequestHelper->getParams($request);
        $username = isset($requestParams['username']) ? $requestParams['username'] : null;
        $password = isset($requestParams['password']) ? $requestParams['password'] : null;

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

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return $this->jsonResponseHelper->createErrorException($exception);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
