<?php

namespace App\Controller;

use App\Entity\Associate;
use App\Entity\Page;
use App\Form\AssociateType;
use App\Service\ProfileManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    private $requestStack;
    private $security;

    public function __construct(
        ProfileManager $profileManager,
        RequestStack $requestStack,
        Security $security,
    )
    {
        $this->manager = $profileManager;
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

        $viewpoint = $this->manager->getViewpoint();

        return $this->render('profile/index.html.twig', [
            'specials' => $this->manager->getSpecialPosts($viewpoint),
            'birthdays' => $this->manager->getBirthdays(),
        ]);
    }

    #[Route('/gebruiker', name: '_reset', methods: ['GET'])]
    public function reset(Request $request): Response
    {
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $this->manager->setViewpoint(false);

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/deelnemer/{id}', name: '_select', methods: ['GET'])]
    public function select(Associate $associate, Request $request): Response
    {
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($associate->getUser() !== $this->getUser()) throw $this->createAccessDeniedException();
        $this->manager->setViewpoint($associate);

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/deelnemer/{id}/show', name: '_show', methods: ['GET'])]
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
    public function posts(Request $request): Response
    {
        $viewpoint = $this->manager->getViewpoint();

        $pages = $this->manager->getPostPages($viewpoint);
        $page = $this->manager->getRequestedPage($request, $pages); 

        return $this->render('post/index.html.twig', [
            'pinned' => $this->manager->getPinnedPosts($viewpoint),
            'posts' => $this->manager->getPosts($viewpoint, $page),
            'page' => $page,
            'pages' => $pages,
        ]);
    }

    #[Route('/berichten/{id}', name: '_post', methods: ['GET'])]
    public function post(string $id): Response
    {
        $viewpoint = $this->manager->getViewpoint();

        if (!($post = $this->manager->getPost($viewpoint, $id))) {
            return $this->redirectToRoute('profile_index');
        }

        return $this->render('post/item.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/kalender', name: '_events', methods: ['GET'])]
    public function events(): Response
    {
        $viewpoint = $this->manager->getViewpoint();

        return $this->render('event/index.html.twig', [
            'events' => $this->manager->getPeriodEvents($viewpoint),
        ]);
    }

    #[Route('/kalender/{id}', name: '_event', methods: ['GET'])]
    public function event(string $id): Response
    {
        $viewpoint = $this->manager->getViewpoint();

        return $this->render('event/item.html.twig', [
            'event' => $this->manager->getEvent($viewpoint, $id),
        ]);
    }

    #[Route('/documenten', name: '_documents', methods: ['GET'])]
    public function documents(Request $request): Response
    {
        $viewpoint = $this->manager->getViewpoint();

        $pages = $this->manager->getDocumentPages($viewpoint);
        $page = $this->manager->getRequestedPage($request, $pages); 

        return $this->render('document/index.html.twig', [
            'pinned' => $this->manager->getPinnedDocuments($viewpoint),
            'documents' => $this->manager->getDocuments($viewpoint, $page),
            'page' => $page,
            'pages' => $pages,
        ]);
    }

    #[Route('/documenten/{id}', name: '_document', methods: ['GET'])]
    public function document(string $id): Response
    {
        $viewpoint = $this->manager->getViewpoint();

        if (!($document = $this->manager->getDocument($viewpoint, $id))) {
            return $this->redirectToRoute('profile_index');
        }

        return $this->render('document/item.html.twig', [
            'document' => $document,
        ]);
    }

    #[Route('/documenten/{id}/download', name: '_download', methods: ['GET'])]
    public function download(string $id): BinaryFileResponse
    {
        $viewpoint = $this->manager->getViewpoint();

        if (!($document = $this->manager->getDocument($viewpoint, $id))) throw $this->createAccessDeniedException();

        $file = $this->getParameter('kernel.project_dir').
                $this->getParameter('app.path.private').
                $this->getParameter('app.path.documents').
                '/'.$document->getDocumentName();

        return $this->file($file, $document->getTitle().'.pdf');
    }

    #[Route('/zoekertjes', name: '_adverts', methods: ['GET'])]
    public function adverts(Request $request): Response
    {
        $pages = $this->manager->getAdvertPages();
        $page = $this->manager->getRequestedPage($request, $pages);

        return $this->render('advert/index.html.twig', [
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
            'advert' => $advert,
        ]);
    }

    #[Route('/faq', name: '_faq', methods: ['GET'])]
    public function faq(Request $request): Response
    {
        $contact = $this->getDoctrine()
            ->getRepository(Page::class)
            ->findOneBySlug('contact')
            ;

        return $this->render('faq/index.html.twig', [
            'faqs' => $this->manager->getFAQs(),
            'contact' => $contact,
        ]);
    }
}
