<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Notifier\CustomLoginLinkNotification;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;

class SecurityController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
        private RequestStack $requestStack,
    )
    {}

    #[Route('/login', name: 'security_signin')]
    public function signin(AuthenticationUtils $authenticationUtils): Response
    {
        // redirect if user is already logged in
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/signin.html.twig', [
            'controller_name' => 'LoginController',
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/login-check', name: 'security_check')]
    public function check(Request $request)
    {
        // get the login link query parameters
        $expires = $request->query->get('expires');
        $username = $request->query->get('user');
        $hash = $request->query->get('hash');

        // and render a template with the button
        return $this->render('security/process_signin_link.html.twig', [
            'expires' => $expires,
            'user' => $username,
            'hash' => $hash,
        ]);
    }

    #[Route('/login-url', name: 'security_signin_link')]
    public function signin_link(
        NotifierInterface $notifier,
        LoginLinkHandlerInterface $loginLinkHandler,
        UserRepository $userRepository,
        Request $request
    )
    {
        // redirect if user is already logged in
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);

        // check if login form is submitted
        if ($request->isMethod('POST')) {

            $session = $this->requestStack->getSession();
            $session->getFlashBag()->add(
                'alert-success',
                'Er is een e-mail met de login link verzonden indien het adres bij ons gekend is.'
            );

            // load the user in some way (e.g. using the form input)
            $email = strtolower($request->request->get('email'));
            $user = $userRepository->findOneBy(['email' => $email]);

            if ($user && $user->isEnabled()) {

                // create a login link for $user this returns an instance
                // of LoginLinkDetails
                $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
                $loginLink = $loginLinkDetails->getUrl();

                // create a notification based on the login link details
                $notification = new CustomLoginLinkNotification(
                    $loginLinkDetails,
                    'Inloggen bij leden-vzw-lach (HGCVHKV)' // email subject
                );
                // create a recipient for this user
                $recipient = new Recipient($email);

                // send the notification to the user
                $notifier->send($notification, $recipient);

            }

            // render a "Login link is sent!" page
            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        // if it's not submitted, render the "login" form
        return $this->render('security/signin_link.html.twig');
    }

    #[Route('/loguit', name: 'security_signout', methods: ['GET'])]
    public function signout(): Response
    {
        // controller can be blank: it will never be called!
        throw new \LogicException('Don\'t forget to activate logout in security.yaml');
    }

    #[Route('/force-relogin', name: 'security_force_relogin', methods: ['GET'])]
    public function force_relogin(Request $request): Response
    {
        $proceed = $request->query->get('proceed');

        if ($proceed) {

            $session = $this->requestStack->getSession();
            $email = $request->query->get('email');
            $user = $this->getUser();

            if ($email === $user->getEmail()) {

                $user->forceRelogin();

                $this->em->persist($user);
                $this->em->flush();

                $session->getFlashBag()->add(
                    'alert-success',
                    'Je wordt overal uitgelogd.'
                );

                $this->logger->debug(sprintf(
                    "User-id %s requested to force relogin at %s from %s.",
                    $user, $user->getForcedReloginAt()->format('r'), $request->getClientIp()
                ));
            } else {
                $session->getFlashBag()->add(
                    'alert-warning',
                    'Het e-mailadres komt niet overeen.'
                );
            }
        }

        return $this->redirectToRoute('profile_index');
    }
}
