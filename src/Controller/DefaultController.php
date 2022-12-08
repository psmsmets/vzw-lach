<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Page;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'Leden vzw LA:CH',
        ]);
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
