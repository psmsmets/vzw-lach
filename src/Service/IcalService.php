<?php

namespace App\Service;

use App\Entity\Associate;
use App\Entity\User;
use App\Service\ProfileManager;
use Kigkonsult\Icalcreator\Vcalendar;
use Kigkonsult\Icalcreator\Vevent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
#use Symfony\Component\HttpFoundation\StreamedResponse;

class IcalService {

    private $manager;

    public function __construct(
        private UrlGeneratorInterface $router,
        private ProfileManager $profileManager,
    ) {
        $this->manager = $profileManager;
    }

    public function createVcalendarResponse($obj, Request $request, string $token) : Response
    {
        // https://github.com/iCalcreator/iCalcreator
        $tz = "Europe/Brussels";
        setlocale(LC_ALL, 'nl_BE');

        // url to route
        $routeName = $request->attributes->get('_route');
        $routeParameters = $request->attributes->get('_route_params');
        $routeParameters['token'] = $token;
        $routeUrl = $this->router->generate($routeName, $routeParameters, UrlGeneratorInterface::ABSOLUTE_URL);
        // https and http to webcal
        $routeUrl = str_replace("http:", "webcal:", str_replace("https:", "webcal:", $routeUrl));

        // cal name and description
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
        $vcalendar = Vcalendar::factory([Vcalendar::UNIQUE_ID => "leden-vzw-lach.be"])
            //->setMethod(Vcalendar::PUBLISH)
            ->setMethod(Vcalendar::REFRESH)
            ->setRefreshinterval('PT1H')
            ->setDescription($caldesc)
            ->setUrl($routeUrl)
            ->setColor('176:24:36')
            ->setXprop('X-PUBLISHED-TTL', 'PT1H')
            ->setXprop(Vcalendar::X_WR_CALNAME, $calname)
            ->setXprop(Vcalendar::X_WR_CALDESC, $caldesc)
            ->setXprop(Vcalendar::X_WR_RELCALID, $obj->getId()->toRfc4122())
            ->setXprop(Vcalendar::X_WR_TIMEZONE, $tz)
            ;

        // get events from obj [Associate or User]
        $events = $this->manager->getPeriodEvents($obj);

        // formatted by locale
        $fmt = new \IntlDateFormatter('nl_BE', \IntlDateFormatter::LONG, \IntlDateFormatter::SHORT);

        // add Event to the Vcalendar
        foreach ($events as $event){

            $url = $this->router->generate('profile_event', ['id' => $event->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

            $desc = [sprintf("Url: %s", $url)];
            if ($event->isCancelled()) $desc[] = sprintf(
                "[GEANNULEERD]\nDit event is geannuleerd op %s.", $fmt->format($event->getCancelledAt())
            );
            $desc[] = sprintf("%s", $event->getText());
            if ($event->getUrl()) $desc[] = sprintf("Meer informatie: %s", $event->getUrl());

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
                ->setSummary(
                    trim(sprintf("%s %s",
                        $event->isCancelled() ? '[GEANNULEERD]' : '',
                        $event->getTitle()
                    ))
                )
                ->setLocation($event->getLocation())
                ->setDescription(implode("\n\n", $desc))
                //->setUrl($url)
                ->setOrganizer(
                    'noreply@leden-vzw-lach.be',
                    [Vcalendar::CN => 'HGCVHKV']
                )
                ;

            // set attendees
            if ($obj instanceof Associate) {

                $this->setVcalendarAttendee($vevent, $obj);

            } else {

                $categories = $event->getCategories();

                if (count($categories) > 0) {
                    // evaluate all category associates
                    foreach ($categories as $category) {
                        foreach ($category->getEnabledAssociates() as $associate) {
                            // let only associates that match the user attend
                            if ($associate->getUser() === $obj) $this->setVcalendarAttendee($vevent, $associate);
                        }
                    }
                } else {
                    // all user associates attend
                    foreach ($obj->getEnabledAssociates() as $associate) {
                        $this->setVcalendarAttendee($vevent, $associate);
                    }
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
