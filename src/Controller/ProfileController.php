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
use Symfony\Component\Uid\Uuid;

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
            'specials' => $this->manager->getSpecialPosts($user),
            'events' => $this->manager->getUpcomingEvents($user),
        ]);
    }

    #[Route('/deelnemer/{id}', name: '_show', methods: ['GET'])]
    public function show(Associate $associate): Response
    {
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($associate->getUser() !== $this->getUser()) throw $this->createAccessDeniedException();

        return $this->render('profile/index.html.twig', [
            'specials' => $this->manager->getSpecialPosts($associate),
            'events' => $this->manager->getUpcomingEvents($associate),
        ]);
    }

    #[Route('/deelnemer/{id}/detail', name: '_detail', methods: ['GET'])]
    public function detail(Associate $associate): Response
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
    public function posts(Request $request): Response
    {
        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $pages = $this->manager->getPostPages($user);
        $page = $this->getRequestedPage($request, $pages); 

        return $this->render('post/index.html.twig', [
            'pinned' => $this->manager->getPinnedPosts($user),
            'posts' => $this->manager->getPosts($user, $page),
            'page' => $page,
            'pages' => $pages,
        ]);
    }

    #[Route('/berichten/{uuid}', name: '_post', methods: ['GET'])]
    public function post(string $uuid): Response
    {
        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!($post = $this->manager->getPost($user, $uuid))) {
            return $this->redirectToRoute('profile_index');
        }

        return $this->render('post/item.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/kalender', name: '_events', methods: ['GET'])]
    public function events(): Response
    {
        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->render('event/index.html.twig', [
            'events' => $this->manager->getPeriodEvents($user),
        ]);
    }

    #[Route('/kalender/{uuid}', name: '_event', methods: ['GET'])]
    public function event(string $uuid): Response
    {
        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->render('event/item.html.twig', [
            'event' => $this->manager->getEvent($user, $uuid),
        ]);
    }

    #[Route('/zoekertjes', name: '_adverts', methods: ['GET'])]
    public function adverts(Request $request): Response
    {
        $pages = $this->manager->getAdvertPages();
        $page = $this->getRequestedPage($request, $pages);

        return $this->render('advert/index.html.twig', [
            'adverts' => $this->manager->getAdverts($page),
            'page' => $page,
            'pages' => $pages,
        ]);
    }

    #[Route('/zoekertjes/{uuid}', name: '_advert', methods: ['GET'])]
    public function advert(string $uuid): Response
    {
        if (!($advert = $this->manager->getAdvert($uuid))) {
            return $this->redirectToRoute('profile_index');
        }

        return $this->render('advert/item.html.twig', [
            'advert' => $advert,
        ]);
    }

    private function getRequestedPage(Request $request, int $pages): int
    {
        $page = (int) $request->query->get('pagina', 1);
        $page = $page < 1 ? 1 : $page;
        $page = $page > $pages ? $pages : $page;

        return $page;
    }
}
