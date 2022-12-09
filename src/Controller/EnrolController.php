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
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

use App\ImageOptimizer;

#[Route('/inschrijven')]
class EnrolController extends AbstractController
{
    private $doctrine;
    private $entityManager;
    private $requestStack;
    private $imageOptimizer;
    private $params;

    public function __construct(
        ManagerRegistry $doctrine,
        EntityManagerInterface $entityManager,
        RequestStack $requestStack,
        ImageOptimizer $imageOptimizer,
        ParameterBagInterface $parameterBag,
    )
    {
        $this->doctrine = $doctrine;
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
        $this->imageOptimizer = $imageOptimizer;
        $this->params = $parameterBag;

        // Accessing the session in the constructor is *NOT* recommended, since
        // it might not be accessible yet or lead to unwanted side-effects
        // $this->session = $requestStack->getSession();
    }

    #[Route('/', name: 'enrol_index', methods: ['GET'])]
    public function index($name = null): Response
    {
        $session = $this->requestStack->getSession();
        $enrolled = $session->get('enrolled', false);
        $session->set('enrolled', false);
        //$session->clear();

        return $this->render('enrol/index.html.twig', ['enrolled' => $enrolled]);
    }

    #[Route('/stap-1', name: 'enrol_user', methods: ['GET', 'POST'])]
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
            if ($user->countAssociates() > 0) {
                $session->getFlashBag()->add('alert-secondary',
                    'Je hebt al ' . $user->countAssociates() .
                    ' eerdere inschrijving(en) op dit e-mailadres: ' . $user->getAssociateNames(0)
                );
            }
            $session->set('user', $user);

            return $this->redirectToRoute('enrol_associate_base', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('enrol/form.html.twig', [
            'enrol_step' => 1,
            'enrol_title' => 'Contactgegevens',
            'enrol_info' => '<p>Wens je meerdere personen in te schrijven op één e-mailadres, dan kan je hier steeds dezelfde gegevens invullen.<br/>In de volgende stappen vragen we de specifieke gegevens voor elke deelnemer.</p>',
            'enrol_prev' => false,
            'enrol_btn' => 'Volgende',
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/stap-2', name: 'enrol_associate_base', methods: ['GET', 'POST'])]
    public function newAssociate(Request $request): Response
    {
        $session = $this->requestStack->getSession();

        $user = $session->get('user', false);
        if (!$user) return $this->redirectToRoute('enrol_user', [], Response::HTTP_SEE_OTHER);

        if (!($associate = $this->doctrine->getRepository(Associate::class)->findOneById($session->get('associate')))) {
            $associate = new Associate();
            $associate->setUser($user);
        }

        $form = $this->createForm(AssociateBaseType::class, $associate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$session->get('associate', false)) {
                $this->entityManager->merge($associate);
            }

            // resize images
            $root = $this->params->get('kernel.project_dir').$this->params->get('app.path.public');
            if (($img = $associate->getImagePortrait())) {
                $this->imageOptimizer->resize($root.$this->params->get('app.path.associates.portrait').'/'.$img);
            }
            if (($img = $associate->getImageEntire())) {
                $this->imageOptimizer->resize($root.$this->params->get('app.path.associates.entire').'/'.$img);
            }

            $this->entityManager->flush();

            $session->set('associate', $associate->getId());

            return $this->redirectToRoute('enrol_associate_details', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('enrol/form.html.twig', [
            'enrol_step' => 2,
            'enrol_title' => 'Deelnemer',
            'enrol_info' => '',
            'enrol_prev' => 'enrol_user',
            'enrol_btn' => 'Volgende',
            'user' => $user,
            'associate' => $associate,
            'form' => $form,
        ]);
    }

    #[Route('/stap-3', name: 'enrol_associate_details', methods: ['GET', 'POST'])]
    public function setAssociateDetails(Request $request): Response
    {
        $session = $this->requestStack->getSession();

        $user = $session->get('user', false);
        if (!$user) return $this->redirectToRoute('enrol_user', [], Response::HTTP_SEE_OTHER);

        $associate = $this->doctrine->getRepository(Associate::class)->findOneById($session->get('associate'));
        if (!$associate) return $this->redirectToRoute('enrol_associate_base', [], Response::HTTP_SEE_OTHER);

        $form = $this->createForm(AssociateDetailsType::class, $associate->getDetails());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('enrol_associate_address', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('enrol/form.html.twig', [
            'enrol_step' => 3,
            'enrol_title' => 'Gegevens',
            'enrol_info' => '',
            'enrol_prev' => 'enrol_associate_base',
            'enrol_btn' => 'Volgende',
            'user' => $user,
            'associate' => $associate,
            'form' => $form,
        ]);
    }

    #[Route('/stap-4', name: 'enrol_associate_address', methods: ['GET', 'POST'])]
    public function setAssociateAddress(Request $request): Response
    {
        $session = $this->requestStack->getSession();

        $user = $session->get('user', false);
        if (!$user) return $this->redirectToRoute('enrol_user', [], Response::HTTP_SEE_OTHER);

        $associate = $this->doctrine->getRepository(Associate::class)->findOneById($session->get('associate'));
        if (!$associate) return $this->redirectToRoute('enrol_associate_base', [], Response::HTTP_SEE_OTHER);

        $form = $this->createForm(AssociateAddressType::class, $associate->getAddress());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('enrol_associate_declarations', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('enrol/form.html.twig', [
            'enrol_step' => 4,
            'enrol_title' => 'Adres',
            'enrol_info' => '',
            'enrol_prev' => 'enrol_associate_details',
            'enrol_btn' => 'Volgende',
            'user' => $user,
            'associate' => $associate,
            'form' => $form,
        ]);
    }


    #[Route('/stap-5', name: 'enrol_associate_declarations', methods: ['GET', 'POST'])]
    public function setAssociateDeclarations(Request $request): Response
    {
        $session = $this->requestStack->getSession();

        $user = $session->get('user', false);
        if (!$user) return $this->redirectToRoute('enrol_user', [], Response::HTTP_SEE_OTHER);

        $associate = $this->doctrine->getRepository(Associate::class)->findOneById($session->get('associate'));
        if (!$associate) return $this->redirectToRoute('enrol_associate_base', [], Response::HTTP_SEE_OTHER);

        $form = $this->createForm(AssociateDeclarationsType::class, $associate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $session->clear();
            $session->set('enrolled', true);
            $session->getFlashBag()->add('alert-success', $associate->getFullName() . ' is ingeschreven');

            return $this->redirectToRoute('enrol_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('enrol/form.html.twig', [
            'enrol_step' => 5,
            'enrol_title' => 'Voorwaarden',
            'enrol_info' => '',
            'enrol_prev' => 'enrol_associate_address',
            'enrol_btn' => 'Verzenden',
            'user' => $user,
            'associate' => $associate,
            'form' => $form,
        ]);
    }
}
