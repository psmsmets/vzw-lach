<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

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

        if (!$user->isEnabled() or count($user->getEnabledAssociates()) == 0)
        {
            $session = $this->requestStack->getSession();
            $session->getFlashBag()->add('alert-warning', 'Inloggen succesvol, maar je profiel is helaas niet actief.');
            throw new AccessDeniedHttpException('Je profiel is niet actief!');

            // log
            $this->logger->info(sprintf(
                "User-id %s attempted to login from %s but is not granted.",
                $user, $event->getRequest()->getClientIp()
            ));
        }
    }

    public function onLogin(LoginSuccessEvent $event): void
    {
        // get user
        $user = $event->getPassport()->getUser();

        // provide feedback
        $session = $this->requestStack->getSession();
        $session->getFlashBag()->add('alert-success', 'Inloggen succesvol. Je bent nu ingelogd.');

        // set user csrf token and store in the session
        if (!$user->getCsrfToken()) $user->setCsrfToken();
        $session->set('csrf_token', $user->getCsrfToken());

        // update last login at
        $user->setLastloginAt();
        $this->em->persist($user);
        $this->em->flush();

        // add to logs
        $this->logger->info(sprintf(
            "User-id %s successfully logged in from %s.",
            $user, $event->getRequest()->getClientIp()
        ));
    }
}
