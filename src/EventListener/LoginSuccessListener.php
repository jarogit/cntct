<?php

namespace App\EventListener;

use App\Entity\ApiToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginSuccessListener implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function onLoginSuccess(LoginSuccessEvent $event)
    {
        if (!$event->getAuthenticator() instanceof \App\Security\ApiKeyAuthenticator) {
            return;
        }

        $user = $event->getUser();

        $token = new ApiToken();
        $token->setUser($user);
        $this->em->persist($token);
        $this->em->flush();

        $event->getResponse()->headers->setCookie(
            new Cookie(
                "X-AUTH-TOKEN",
                $token->getToken(),
                (new \DateTime)->modify('+1 day'),
                '/',
                null,
                false,
                false
            )
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
        ];
    }
}