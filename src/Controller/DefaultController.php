<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'Leden vzw LA:CH',
        ]);
    }

    #[Route('/voorwaarden', name: 'voorwaarden')]
    public function static_voorwaarden(): Response
    {
        return $this->render('default/voorwaarden.html.twig', []);
    }
}
