<?php
/**
 * Created by PhpStorm.
 * User: bmnk
 * Date: 04/04/18
 * Time: 21:44
 */

namespace App\Controller;


use App\Entity\Room;
use App\Service\Calendar;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends Controller
{
    /**
     * @Route("/room/{id}/calendar", name="room_calendar_show", methods="GET")
     * @param Room $room
     * @param Calendar $calendar
     * @return Response
     */
    public function show(Room $room, Calendar $calendar): Response
    {
        dump($room);
        dump($calendar->toString());

        $calendarToString = $calendar->toString();

        $weeks = $calendar->getWeeks();

        $days = $calendar->getDays();

        $firstDay = $calendar->getFirstDay()->modify('last monday');

        dump($weeks);
        dump($days);
        return $this->render('calendar/show.html.twig', [
            'room' => $room,
            'calendar' => $calendar,
            'calendarToString' => $calendarToString,
            'weeks' => $weeks,
            'firstDay' => $firstDay,
            'days' => $days,
        ]);
    }
}