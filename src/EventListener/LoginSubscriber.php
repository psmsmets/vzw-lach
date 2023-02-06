<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class LoginSubscriber implements EventSubscriberInterface
{
    private $requestStack;

    public function __construct(
        RequestStack $requestStack,
    ) {
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents(): array
    {
        return [CheckPassportEvent::class => 'preLogin', 
                LoginSuccessEvent::class => 'onLogin'];
    }

    public function preLogin(CheckPassportEvent $event): void
    {
        $user = $event->getPassport()->getUser();
        if (!$user->isEnabled() or count($user->getEnabledAssociates()) == 0)
        {
            $session = $this->requestStack->getSession();
            $session->getFlashBag()->add('alert-warning', 'Inloggen succesvol, maar je profiel is helaas niet actief.');
            throw new AccessDeniedHttpException('Je profiel is niet actief!');
        }
    }

    public function onLogin(LoginSuccessEvent $event): void
    {
        // provide feedback
        $session = $this->requestStack->getSession();
        $session->getFlashBag()->add('alert-success', 'Inloggen succesvol. Je bent nu ingelogd.');
    }
}
