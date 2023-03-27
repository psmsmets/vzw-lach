<?php

namespace App\Controller;

use App\Entity\Associate;
use App\Entity\User;
use App\Service\ProfileManager;
use App\Service\IcalService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Uid\Uuid;

#[Route('/api', name: 'api')]
class ApiController extends AbstractController
{
    public function __construct(
        private LoggerInterface $logger,
        private ProfileManager $manager,
        private IcalService $ical,
    )
    {}

    #[Route('/ical/a/{id}/hgcvhkv.ics', name: '_ical_events_associate', methods: ['GET'])]
    public function ical_events_associate(Associate $associate, Request $request): Response
    {
        $token = $request->query->get('token');
        if ($associate->getUser()->getIcalToken() !== $token) throw $this->createAccessDeniedException();

        $this->logger->debug(sprintf("Associate-id %s succesfully requested the ical object.", $associate));

        return $this->ical->createVcalendarResponse($associate, $request, $token);
    }

    #[Route('/ical/u/{id}/hgcvhkv.ics', name: '_ical_events_user', methods: ['GET'])]
    public function ical_events_user(User $user, Request $request): Response
    {
        $token = $request->query->get('token');
        if ($user->getIcalToken() !== $token) throw $this->createAccessDeniedException();

        $this->logger->debug(sprintf("User-id %s succesfully requested the ical object.", $user));

        return $this->ical->createVcalendarResponse($user, $request, $token);
    }

    #[Route('/private/adverts', name: '_adverts', methods: ['GET'])]
    public function load_adverts(Request $request): Response
    {
        $viewpoint = $this->manager->getViewpoint();

        return $this->json([
            'success' => true,
            'html' => $this->render('advert/module_content.html.twig', [
                'adverts' => $this->manager->getSpecialAdverts($viewpoint),
            ])->getContent(),
        ]);

    }

    #[Route('/private/documents', name: '_documents', methods: ['GET'])]
    public function load_documents(Request $request): Response
    {
        $viewpoint = $this->manager->getViewpoint();

        return $this->json([
            'success' => true,
            'html' => $this->render('document/module_content.html.twig', [
                'documents' => $this->manager->getSpecialDocuments($viewpoint),
            ])->getContent(),
        ]);
    }

    #[Route('/private/upcoming-events', name: '_upcoming_events', methods: ['GET'])]
    public function load_upcoming_events(Request $request): Response
    {
        $viewpoint = $this->manager->getViewpoint();

        return $this->json([
            'success' => true,
            'html' => $this->render('event/module_content.html.twig', [
                'events' => $this->manager->getUpcomingEvents($viewpoint, 6, null),
            ])->getContent(),
        ]);
    }

    #[Route('/private/period-events', name: '_period_events', methods: ['GET'])]
    public function load_period_events(Request $request): Response
    {
        $viewpoint = $this->manager->getViewpoint();

        return $this->json([
            'success' => true,
            'html' => $this->render('event/index2_content.html.twig', [
                'events' => $this->manager->getPeriodEvents($viewpoint),
            ])->getContent(),
        ]);
    }
}
