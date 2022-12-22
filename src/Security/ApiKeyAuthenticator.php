<?php

namespace App\Security;

use App\Model\Error400;
use App\Repository\ApiTokenRepository;
use App\Service\ApiResponseSerializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private ApiTokenRepository $tokenRepository, private ApiResponseSerializer $serializer
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return true;
    }

    public function authenticate(Request $request): Passport
    {
        $apiToken = $request->headers->get('X-AUTH-TOKEN');
        if (!$apiToken) {
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        return new SelfValidatingPassport(
            new UserBadge($apiToken, function($apiToken) {
                $token = $this->tokenRepository->find($apiToken);
                if (!$token) {
                    throw new UserNotFoundException();
                }

                return $token->getUser();
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $model = new Error400\General(Error400::UNAUTHORIZED);
        $model->message = strtr($exception->getMessageKey(), $exception->getMessageData());

        $response = $this->serializer->serialize($model);
        $response->setStatusCode(Response::HTTP_UNAUTHORIZED);

        return $response;
    }
}