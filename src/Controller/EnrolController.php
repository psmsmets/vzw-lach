<?php

namespace App\Controller;

use App\Entity\Associate;
use App\Form\AssociateType;
use App\Form\AssociateBaseType;
use App\Form\AssociateDeclarationsType;
use App\Repository\AssociateRepository;

use App\Entity\AssociateDetails;
use App\Form\AssociateDetailsType;
use App\Repository\AssociateDetailsRepository;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;

#[Route('/inschrijven')]
class EnrolController extends AbstractController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;

        // Accessing the session in the constructor is *NOT* recommended, since
        // it might not be accessible yet or lead to unwanted side-effects
        // $this->session = $requestStack->getSession();
    }

    #[Route('/', name: 'app_enrol_index', methods: ['GET'])]
    public function index(AssociateRepository $associateRepository): Response
    {
        return $this->render('enrol/index.html.twig', [
            'associates' => $associateRepository->findAll(),
        ]);
    }

    #[Route('/stap-1', name: 'app_enrol_user', methods: ['GET', 'POST'])]
    public function newUser(Request $request, UserRepository $userRepository): Response
    {
        $session = $this->requestStack->getSession();
        $user = $session->get('user', new User());

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!($user = $userRepository->findOneByUserIdentifier($user->getUserIdentifier()))) {
                $user = $form->getData();
                $userRepository->save($user, true);
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
    public function newAssociate(Request $request, AssociateRepository $associateRepository): Response
    {
        $session = $this->requestStack->getSession();

        $user = $session->get('user', false);
        if (!$user) return $this->redirectToRoute('app_enrol_user', [], Response::HTTP_SEE_OTHER);

        $associate = $session->get('associate', new Associate());

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
    public function setAssociateDetails(Request $request, AssociateRepository $associateRepository): Response
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
            return $this->redirectToRoute('app_enrol_associate_declarations', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('enrol/form.html.twig', [
            'enrol_step' => 3,
            'enrol_title' => 'Deelnemer',
            'enrol_prev' => 'app_enrol_associate_base',
            'enrol_btn' => 'Volgende',
            'user' => $user,
            'associate' => $associate,
            'form' => $form,

        ]);
    }

    #[Route('/stap-4', name: 'app_enrol_associate_declarations', methods: ['GET', 'POST'])]
    public function setAssociateDeclarations(Request $request, AssociateRepository $associateRepository): Response
    {
        $session = $this->requestStack->getSession();

        $user = $session->get('user', false);
        if (!$user) return $this->redirectToRoute('app_enrol_user', [], Response::HTTP_SEE_OTHER);

        $associate = $session->get('associate', false);
        if (!$associate) return $this->redirectToRoute('app_enrol_associate_base', [], Response::HTTP_SEE_OTHER);

        $form = $this->createForm(AssociateDeclarationsType::class, $associate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $associateRepository->save($associate, true);
            $session->getFlashBag()->add('notice', $associate->getName() . ' is ingeschreven');
            $session->clear();
            $session->set('user', $user);
            return $this->redirectToRoute('app_enrol_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('enrol/form.html.twig', [
            'enrol_step' => 4,
            'enrol_title' => 'Voorwaarden',
            'enrol_prev' => 'app_enrol_associate_details',
            'enrol_btn' => 'Verzenden',
            'user' => $user,
            'associate' => $associate,
            'form' => $form,
        ]);
    }
}
