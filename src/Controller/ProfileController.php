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
    public function index(Request $request): Response
    {
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $viewpoint = $this->getViewpoint();

        return $this->render('profile/index.html.twig', [
            'viewpoint' => $viewpoint,
            'specials' => $this->manager->getSpecialPosts($viewpoint),
            'events' => $this->manager->getUpcomingEvents($viewpoint),
        ]);
    }

    #[Route('/gebruiker', name: '_reset', methods: ['GET'])]
    public function reset(Request $request): Response
    {
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $this->setViewpoint(false);

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/deelnemer/{id}', name: '_select', methods: ['GET'])]
    public function select(Associate $associate, Request $request): Response
    {
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($associate->getUser() !== $this->getUser()) throw $this->createAccessDeniedException();
        $this->setViewpoint($associate);

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/deelnemer/{id}/show', name: '_show', methods: ['GET'])]
    public function show(Associate $associate): Response
    {
        if ($associate->getUser() !== $this->getUser()) throw $this->createAccessDeniedException();
        $viewpoint = $this->getViewpoint();

        return $this->render('profile/show.html.twig', [
            'viewpoint' => $viewpoint,
            'associate' => $associate,
        ]);
    }

    #[Route('/deelnemer/{id}/bewerk', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Associate $associate): Response
    {
        if ($associate->getUser() !== $this->getUser()) throw $this->createAccessDeniedException();
        $viewpoint = $this->getViewpoint();

        $form = $this->createForm(AssociateType::class, $associate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->associateRepository->save($associate, true);

            return $this->redirectToRoute('profile_show', ['id' => $associate->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profile/edit.html.twig', [
            'viewpoint' => $viewpoint,
            'associate' => $associate,
            'form' => $form,
        ]);
    }

    #[Route('/berichten', name: '_posts', methods: ['GET'])]
    public function posts(Request $request): Response
    {
        $viewpoint = $this->getViewpoint();

        $pages = $this->manager->getPostPages($viewpoint);
        $page = $this->getRequestedPage($request, $pages); 

        return $this->render('post/index.html.twig', [
            'viewpoint' => $viewpoint,
            'pinned' => $this->manager->getPinnedPosts($viewpoint),
            'posts' => $this->manager->getPosts($viewpoint, $page),
            'page' => $page,
            'pages' => $pages,
        ]);
    }

    #[Route('/berichten/{id}', name: '_post', methods: ['GET'])]
    public function post(string $id): Response
    {
        $viewpoint = $this->getViewpoint();

        if (!($post = $this->manager->getPost($viewpoint, $id))) {
            return $this->redirectToRoute('profile_index');
        }

        return $this->render('post/item.html.twig', [
            'viewpoint' => $viewpoint,
            'post' => $post,
        ]);
    }

    #[Route('/kalender', name: '_events', methods: ['GET'])]
    public function events(): Response
    {
        $viewpoint = $this->getViewpoint();

        return $this->render('event/index.html.twig', [
            'viewpoint' => $viewpoint,
            'events' => $this->manager->getPeriodEvents($viewpoint),
        ]);
    }

    #[Route('/kalender/{id}', name: '_event', methods: ['GET'])]
    public function event(string $id): Response
    {
        $viewpoint = $this->getViewpoint();

        return $this->render('event/item.html.twig', [
            'viewpoint' => $viewpoint,
            'event' => $this->manager->getEvent($viewpoint, $id),
        ]);
    }

    #[Route('/zoekertjes', name: '_adverts', methods: ['GET'])]
    public function adverts(Request $request): Response
    {
        $viewpoint = $this->getViewpoint();

        $pages = $this->manager->getAdvertPages();
        $page = $this->getRequestedPage($request, $pages);

        return $this->render('advert/index.html.twig', [
            'viewpoint' => $viewpoint,
            'adverts' => $this->manager->getAdverts($page),
            'page' => $page,
            'pages' => $pages,
        ]);
    }

    #[Route('/zoekertjes/{id}', name: '_advert', methods: ['GET'])]
    public function advert(string $id): Response
    {
        if (!($advert = $this->manager->getAdvert($id))) {
            return $this->redirectToRoute('profile_index');
        }

        return $this->render('advert/item.html.twig', [
            'viewpoint' => $viewpoint,
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

    private function getViewpoint()
    {
        $session = $this->requestStack->getSession();
        $viewpoint = $session->get('viewpoint', false);

        if ($viewpoint instanceof Uuid)
        {
            $associate = $this->manager->getAssociate($viewpoint);
            if (!$associate or $associate->getUser() !== $this->getUser()) throw $this->createAccessDeniedException();

            return $associate;
        }
        return $this->getUser();
    }

    private function setViewpoint($viewpoint): self
    {
        $session = $this->requestStack->getSession();
        if ($viewpoint instanceof Associate)
        {
            $session->set('viewpoint', $viewpoint->getId());
            $session->getFlashBag()->add('alert-success', 'Je bekijkt nu enkel de informatie van '.strval($viewpoint));
        } else {
            $session->set('viewpoint', false);
            $session->getFlashBag()->add('alert-success', 'Je bekijkt nu de informatie voor al je deelnemers');
        }

        return $this;
    }
}
