<?php

namespace App\Controller;

use App\Entity\Associate;
use App\Form\AssociateType;
use App\Form\AssociateBaseType;
use App\Form\AssociateDeclarationsType;

use App\Entity\AssociateAddress;
use App\Form\AssociateAddressType;

use App\Entity\AssociateDetails;
use App\Form\AssociateDetailsType;

use App\Entity\User;
use App\Form\UserType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/inschrijven')]
class EnrolController extends AbstractController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack, ManagerRegistry $doctrine, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;

        // Accessing the session in the constructor is *NOT* recommended, since
        // it might not be accessible yet or lead to unwanted side-effects
        // $this->session = $requestStack->getSession();

        $this->doctrine = $doctrine;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_enrol_index', methods: ['GET'])]
    public function index(): Response
    {
        $session = $this->requestStack->getSession();
        $user = $session->clear();

        return $this->render('enrol/index.html.twig', []);
    }

    #[Route('/stap-1', name: 'app_enrol_user', methods: ['GET', 'POST'])]
    public function newUser(Request $request): Response
    {
        $session = $this->requestStack->getSession();
        $user = $session->get('user', new User());

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!($user = $this->doctrine->getRepository(User::class)->findOneByUserIdentifier($user->getUserIdentifier()))) {
                $user = $form->getData();
                $this->entityManager->persist($user);
            }
            $this->entityManager->flush();
            if ( ($userAssociates = count($user->getAssociates())) ) {
                $session->getFlashBag()->add('alert-primary', 'Je hebt al ' . ($userAssociates == 1 ? '1 eerdere inschrijving' : $userAssociates . ' eerdere inschrijvingen') . ' op dit e-mailadres.');
            }
            $session->set('user', $user);

            return $this->redirectToRoute('app_enrol_associate_base', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('enrol/form.html.twig', [
            'enrol_step' => 1,
            'enrol_title' => 'Contactgegevens',
            'enrol_prev' => false,
            'enrol_btn' => 'Volgende',
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/stap-2', name: 'app_enrol_associate_base', methods: ['GET', 'POST'])]
    public function newAssociate(Request $request): Response
    {
        $session = $this->requestStack->getSession();

        $user = $session->get('user', false);
        if (!$user) return $this->redirectToRoute('app_enrol_user', [], Response::HTTP_SEE_OTHER);

        $associate = $session->get('associate', new Associate());
        $associate->setUser($user);

        $form = $this->createForm(AssociateBaseType::class, $associate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->set('associate', $associate);
            return $this->redirectToRoute('app_enrol_associate_details', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('enrol/form.html.twig', [
            'enrol_step' => 2,
            'enrol_title' => 'Deelnemer',
            'enrol_prev' => 'app_enrol_user',
            'enrol_btn' => 'Volgende',
            'user' => $user,
            'associate' => $associate,
            'form' => $form,

        ]);
    }

    #[Route('/stap-3', name: 'app_enrol_associate_details', methods: ['GET', 'POST'])]
    public function setAssociateDetails(Request $request): Response
    {
        $session = $this->requestStack->getSession();

        $user = $session->get('user', false);
        if (!$user) return $this->redirectToRoute('app_enrol_user', [], Response::HTTP_SEE_OTHER);

        $associate = $session->get('associate', false);
        if (!$associate) return $this->redirectToRoute('app_enrol_associate_base', [], Response::HTTP_SEE_OTHER);

        $form = $this->createForm(AssociateDetailsType::class, $associate->getDetails());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->set('associate', $associate);
            return $this->redirectToRoute('app_enrol_associate_address', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('enrol/form.html.twig', [
            'enrol_step' => 3,
            'enrol_title' => 'Gegevens',
            'enrol_prev' => 'app_enrol_associate_base',
            'enrol_btn' => 'Volgende',
            'user' => $user,
            'associate' => $associate,
            'form' => $form,
        ]);
    }

    #[Route('/stap-4', name: 'app_enrol_associate_address', methods: ['GET', 'POST'])]
    public function setAssociateAddress(Request $request): Response
    {
        $session = $this->requestStack->getSession();

        $user = $session->get('user', false);
        if (!$user) return $this->redirectToRoute('app_enrol_user', [], Response::HTTP_SEE_OTHER);

        $associate = $session->get('associate', false);
        if (!$associate) return $this->redirectToRoute('app_enrol_associate_base', [], Response::HTTP_SEE_OTHER);

        $form = $this->createForm(AssociateAddressType::class, $associate->getAddress());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->set('associate', $associate);
            return $this->redirectToRoute('app_enrol_associate_declarations', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('enrol/form.html.twig', [
            'enrol_step' => 4,
            'enrol_title' => 'Adres',
            'enrol_prev' => 'app_enrol_associate_details',
            'enrol_btn' => 'Volgende',
            'user' => $user,
            'associate' => $associate,
            'form' => $form,
        ]);
    }


    #[Route('/stap-5', name: 'app_enrol_associate_declarations', methods: ['GET', 'POST'])]
    public function setAssociateDeclarations(Request $request): Response
    {
        $session = $this->requestStack->getSession();

        $user = $session->get('user', false);
        if (!$user) return $this->redirectToRoute('app_enrol_user', [], Response::HTTP_SEE_OTHER);

        $associate = $session->get('associate', false);
        if (!$associate) return $this->redirectToRoute('app_enrol_associate_base', [], Response::HTTP_SEE_OTHER);

        $form = $this->createForm(AssociateDeclarationsType::class, $associate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->merge($associate);
            $this->entityManager->flush();

            $session->getFlashBag()->add('alert-success', $associate->getName() . ' is ingeschreven');

            $session->clear();
            $session->set('user', $user);

            return $this->redirectToRoute('app_enrol_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('enrol/form.html.twig', [
            'enrol_step' => 5,
            'enrol_title' => 'Voorwaarden',
            'enrol_prev' => 'app_enrol_associate_address',
            'enrol_btn' => 'Verzenden',
            'user' => $user,
            'associate' => $associate,
            'form' => $form,
        ]);
    }
}
