<?php

namespace App\Controller;

use App\Entity\Associate;
use App\Form\AssociateType;
use App\Service\ProfileManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/mijn-profiel', name: 'profile')]
class ProfileController extends AbstractController
{
    private $manager;
    private $parameterBag;
    private $requestStack;
    private $security;

    public function __construct(
        ProfileManager $profileManager,
        ParameterBagInterface $parameterBag,
        RequestStack $requestStack,
        Security $security,
    )
    {
        $this->manager = $profileManager;
        $this->parameterBag = $parameterBag;
        $this->requestStack = $requestStack;
        $this->security = $security;

        // Accessing the session in the constructor is *NOT* recommended, since
        // it might not be accessible yet or lead to unwanted side-effects
        // $this->session = $requestStack->getSession();
    }

    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(): Response
    {

        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->render('profile/index.html.twig', [
            //'associates' => $user->getEnabledAssociates(),
            'special' => $this->manager->getSpecialPosts($user),
            'pinned' => $this->manager->getPinnedPosts($user),
            'posts' => $this->manager->getPosts($user),
            'events' => $this->manager->userEvents($user),
        ]);
    }

    #[Route('/deelnemer/{id}', name: '_show', methods: ['GET'])]
    public function show(Associate $associate): Response
    {

        if ($associate->getUser() !== $this->getUser()) throw $this->createAccessDeniedException();

        return $this->render('profile/show.html.twig', [
            'associate' => $associate,
        ]);
    }

    #[Route('/deelnemer/{id}/bewerk', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Associate $associate): Response
    {
        if ($associate->getUser() !== $this->getUser()) throw $this->createAccessDeniedException();

        $form = $this->createForm(AssociateType::class, $associate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->associateRepository->save($associate, true);

            return $this->redirectToRoute('profile_show', ['id' => $associate->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profile/edit.html.twig', [
            'associate' => $associate,
            'form' => $form,
        ]);
    }

    #[Route('/berichten', name: '_posts', methods: ['GET'])]
    public function posts(): Response
    {

        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->render('profile/posts.html.twig', [
            'posts' => $this->manager->getPosts($user),
        ]);
    }

    #[Route('/kalender', name: '_events', methods: ['GET'])]
    public function events(): Response
    {

        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->render('profile/events.html.twig', [
            'events' => $this->manager->getPeriodEvents($user),
        ]);
    }
}
