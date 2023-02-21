<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

class RequestListener
{
    public function __construct(
        //private LoggerInterface $logger,
        private RequestStack $requestStack,
        private RouterInterface $router,
        private Security $security,
    ) {}

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            // don't do anything if it's not the main request
            return;
        }

        if ($this->security->isGranted('IS_AUTHENTICATED_REMEMBERED')) {

            $user = $this->security->getUser();
            $session = $this->requestStack->getSession();

            if ($session->get('csrf_token') !== $user->getCsrfToken()) {

                // provide user feedback
                $session->getFlashBag()->add(
                    'alert-danger', 'Je sessies zijn verlopen. Je wordt nu uitgelogd op alle toestellen.'
                );

                // add to logs
                // $this->logger->info(sprintf(
                //    "User-id %s from %s was forced to logout due to a csfr token mismatch.",
                //    $user, $event->getRequest()->getClientIp()
                //));

                // redirect
                $response = new RedirectResponse(
                    $this->router->generate('security_signout'),
                    RedirectResponse::HTTP_SEE_OTHER
                );
                $event->setResponse($response);
            }

        }

    }
}
