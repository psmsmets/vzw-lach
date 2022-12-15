<?php

namespace App\Controller;

use App\Entity\Associate;
use App\Form\AssociateType;
use App\Repository\AssociateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/mijn-profiel', name: 'profile')]
class ProfileController extends AbstractController
{
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
            'associates' => $user->getEnabledAssociates(),
        ]);
    }

    #[Route('/{id}', name: '_show', methods: ['GET'])]
    public function show(Associate $associate): Response
    {

        if ($associate->getUser() !== $this->getUser()) throw $this->createAccessDeniedException();

        return $this->render('profile/show.html.twig', [
            'associate' => $associate,
        ]);
    }

    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Associate $associate, AssociateRepository $associateRepository): Response
    {
        if ($associate->getUser() !== $this->getUser()) throw $this->createAccessDeniedException();

        $form = $this->createForm(AssociateType::class, $associate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $associateRepository->save($associate, true);

            return $this->redirectToRoute('profile_show', ['id' => $associate->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profile/edit.html.twig', [
            'associate' => $associate,
            'form' => $form,
        ]);
    }
}
