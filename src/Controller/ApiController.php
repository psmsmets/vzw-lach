<?php

namespace App\Controller;

use App\Entity\Associate;
use App\Entity\User;
use App\Service\ProfileManager;
use Kigkonsult\Icalcreator\Vcalendar;
use Kigkonsult\Icalcreator\Vevent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Security\Core\Security;

#[Route('/api', name: 'api')]
class ApiController extends AbstractController
{
    private $manager;
    private $security;

    public function __construct(ProfileManager $profileManager, Security $security)
    {
        $this->manager = $profileManager;
        $this->security = $security;
    }

    #[Route('/ical/a/{id}.ics', name: '_events_associate', methods: ['GET'])]
    public function events_associate(Associate $associate, Request $request): Response
    {
        return $this->createVcalendarResponse($associate);
    }

    #[Route('/ical/u/{id}.ics', name: '_events_user', methods: ['GET'])]
    public function events_user(User $user, Request $request): Response
    {
        return $this->createVcalendarResponse($user);
    }

    private function createVcalendarResponse($obj) : Response
    {
        // https://github.com/iCalcreator/iCalcreator
        $tz = "Europe/Brussels";

        if ($obj instanceof User) {
            $name = $obj->getEmail();
            $caldesc = sprintf(
                "Dit is de kalender van %s voor de deelnemer(s) %s aan Het Groot Circus van het Klein Verdriet.",
                $name, $obj->getAssociateNames()
            );
            $calid = 'U-';
        } else {
            $name = $obj->getFullName();
            $caldesc = sprintf(
                "Dit is de specifieke kalender voor de deelnemer %s aan Het Groot Circus van het Klein Verdriet.",
                $name,
            );
            $calid = 'A-';
        }
        $calname = sprintf("HGCVHKV %s", $name);
        $calid .= $obj->getId()->toRfc4122();

        // create a new calendar with calendaring info
        $vcalendar = Vcalendar::factory( [ Vcalendar::UNIQUE_ID => "leden-vzw-lach.be", ] )
            ->setMethod(Vcalendar::PUBLISH)
            ->setXprop(Vcalendar::X_WR_CALNAME, $calname)
            ->setXprop(Vcalendar::X_WR_CALDESC, $caldesc)
            ->setXprop(Vcalendar::X_WR_RELCALID, $obj->getId()->toRfc4122())
            ->setXprop(Vcalendar::X_WR_TIMEZONE, $tz)
            ;

        // get events from obj [Associate or User]
        $events = $this->manager->getPeriodEvents($obj);

        // add Event to the Vcalendar
        foreach ($events as $event){

            $vevent = $vcalendar->newVevent()
                ->setUid($event->getId()->toRfc4122())
                ->setDtstart(
                    new \DateTime(
                        $event->trueStartTime()->format('Y-m-d H:i:s'),
                        new \DateTimeZone($tz)
                    )
                )
                ->setDtend(
                    new \DateTime(
                        $event->trueEndTime()->format('Y-m-d H:i:s'),
                        new \DateTimeZone($tz)
                    )
                )
                ->setSummary($event->getTitle())
                ->setLocation($event->getLocation())
                ->setDescription($event->getDescription())
                ->setComment($event->getBody())
                ->setUrl($event->getUrl())
                ->setOrganizer(
                    'noreply@leden-vzw-lach.be',
                    [Vcalendar::CN => 'HGCVHKV']
                )
                ;

            // set attendees
            $categories = $event->getCategories();

            if (count($categories) > 0) {
                foreach ($categories as $category) {
                    foreach ($category->getEnabledAssociates() as $associate) {
                        $this->setVcalendarAttendee($vevent, $associate);
                    }
                }
            } else {
                if ($obj instanceof User) {
                    foreach ($obj->getEnabledAssociates() as $associate) {
                        $this->setVcalendarAttendee($vevent, $associate);
                    }
                } else {
                    $this->setVcalendarAttendee($vevent, $obj);
                }
            }

            // add alarm 1 day before 
            $alarm = $vevent->newValarm()
                ->setAction(Vcalendar::DISPLAY)
                // copy summary and description from event
                ->setSummary($vevent->getSummary())
                ->setDescription($vevent->getDescription())
                // fire off the alarm one day before
                ->setTrigger('-P1D')
                ;
        }

        // create the calendar string
        $vcalendarString = $vcalendar
            // apply appropriate Vtimezone with Standard/DayLight components
            ->vtimezonePopulate()
            // and create the (string) calendar
            ->createCalendar()
            ;

        // replace http:// to webcal:// with either twig or htaccess
        return new Response($vcalendarString, 200, array('Content-Type' => 'text/calendar'));
    }

    private function setVcalendarAttendee(Vevent $vevent, Associate $associate): void
    {
        $email = $associate->getDetails()->getEmail();

        $vevent->setAttendee(
            $email ? $email : $associate->getUser()->getEmail(),
            [
                Vcalendar::ROLE     => Vcalendar::REQ_PARTICIPANT,
                Vcalendar::PARTSTAT => Vcalendar::ACCEPTED,
                Vcalendar::RSVP     => Vcalendar::FALSE,
                Vcalendar::CN       => strval($associate),
            ]
        );
    }
}
