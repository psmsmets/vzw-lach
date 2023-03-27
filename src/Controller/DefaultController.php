<?php

namespace App\Controller;

use App\Entity\Page;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class DefaultController extends AbstractController
{
    public function __construct(
        private Security $security
    )
    {}

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        //return $this->redirectToRoute('enrol_index');
        if ($this->security->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('profile_index');
        }
        return $this->render('default/index_login.html.twig', []);
    }

    #[Route('/admin', name: 'admin_')]
    public function admin(): Response
    {
        return $this->redirectToRoute('admin');
    }

    #[Route('/{slug}', name: 'page')]
    public function page($slug): Response
    {
        $page = $this->getDoctrine()
            ->getRepository(Page::class)
            ->findOneBySlug($slug)
            ;

        if (!$page) throw $this->createNotFoundException();
        if (!$page->isEnabled()) $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('default/page.html.twig', [
            'page' => $page,
        ]);
    }
}
