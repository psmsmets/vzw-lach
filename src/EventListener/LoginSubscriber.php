<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class LoginSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
        private RequestStack $requestStack,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [CheckPassportEvent::class => 'preLogin', 
                LoginSuccessEvent::class => 'onLogin'];
    }

    public function preLogin(CheckPassportEvent $event): void
    {
        $user = $event->getPassport()->getUser();

        // $this->logger->info(sprintf("User-id %s has requested to login.", $user));

        if (!$user->isEnabled() or count($user->getEnabledAssociates()) == 0)
        {
            $session = $this->requestStack->getSession();
            $session->getFlashBag()->add('alert-warning', 'Inloggen succesvol, maar je profiel is helaas niet actief.');
            throw new AccessDeniedHttpException('Je profiel is niet actief!');

            // log
            $this->logger->info(sprintf("User-id %s attempted to login but is is not granted.", $user));
        }
    }

    public function onLogin(LoginSuccessEvent $event): void
    {
        // provide user feedback
        $session = $this->requestStack->getSession();
        $session->getFlashBag()->add('alert-success', 'Inloggen succesvol. Je bent nu ingelogd.');

        // update lastLoginAt
        $user = $event->getPassport()->getUser();
        $user->setLastloginAt();
        $this->em->persist($user);
        $this->em->flush();

        // log
        $this->logger->info(sprintf("User-id %s successfully logged in.", $user));
    }
}
