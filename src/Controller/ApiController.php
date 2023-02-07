<?php

namespace App\Controller;

use App\Entity\Event;
use Spatie\CalendarLinks\Link;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\HttpFoundation\RedirectResponse;

#[Route('/api', name: 'api')]
class ApiController extends AbstractController
{
    #[Route('/ics/{id}', name: '_event', methods: ['GET'])]
    public function event(Event $event, Request $request): Response
    {
        $link = Link::create($event->getTitle(), $event->trueStartTime(), $event->trueEndTime(), $event->isAllDay())
            ->description(is_null($event->getDescription()) ? '' : $event->getDescription())
            ->address($event->getLocation());

        return new RedirectResponse($link->ics());
    }
}
