<?php
/**
 * Created by PhpStorm.
 * User: bmnk
 * Date: 04/04/18
 * Time: 21:44.
 */

namespace App\Controller;

use App\Entity\Room;
use App\Repository\ReservationRepository;
use App\Service\Calendar;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends Controller
{
    /**
     * @Route("/room/{id}/calendar", name="room_calendar_show", methods="GET")
     *
     * @param Room $room
     * @param Calendar $calendar
     *
     * @param ReservationRepository $reservationRepository
     * @return Response
     */
    public function show(Room $room, Calendar $calendar, ReservationRepository $reservationRepository): Response
    {
        // Month and year in string
        $calendarToString = $calendar->toString();

        // number of weeks in the current month
        $weeks = $calendar->getWeeks();

        // Array on weeks days
        $days = $calendar->getDays();

        // fisrt day of the first week
        $firstDay = $calendar->getFirstDay();
        $firstDay = $firstDay->format('N') === '1' ? $firstDay : $calendar->getFirstDay()->modify('last monday');

        // last day of the last week
        $end = (clone $firstDay)->modify('+'.(6 + 7 * ($weeks - 1)). ' days');

        // Current month reservations
        $reservations = $reservationRepository->findBetween($firstDay, $end);
        $monthReservationsByDay = [];
        foreach ($reservations as $reservation){
            $date = $reservation->getStart()->format('Y-m-d');
            if(!isset($monthReservationsByDay[$date])){
                $monthReservationsByDay[$date] = [$reservation];
            }else{
                $monthReservationsByDay[$date][] = $reservation;
            }
        }
        dump($monthReservationsByDay);
        return $this->render('calendar/show.html.twig', [
            'room' => $room,
            'calendar' => $calendar,
            'calendarToString' => $calendarToString,
            'weeks' => $weeks,
            'firstDay' => $firstDay,
            'days' => $days,
            'monthReservationsByDay' => $monthReservationsByDay,
        ]);
    }

    /**
     * @Route("/room/{id}/calendar/{month}/{year}", name="room_calendar_previous", methods="GET")
     *
     * @throws \Exception
     */
    public function previousMonth(Room $room, $month, $year, ReservationRepository $reservationRepository)
    {
        $calendar = new Calendar($month, $year);

        // Month and year in string
        $calendarToString = $calendar->toString();

        // number of weeks in the current month
        $weeks = $calendar->getWeeks();

        // Array on weeks days
        $days = $calendar->getDays();

        // fisrt day of the first week
        $firstDay = $calendar->getFirstDay();
        $firstDay = $firstDay->format('N') === '1' ? $firstDay : $calendar->getFirstDay()->modify('last monday');

        // last day of the last week
        $end = (clone $firstDay)->modify('+'.(6 + 7 * ($weeks - 1)). ' days');

        // Current month reservations
        $monthReservations = $reservationRepository->findBetween($firstDay, $end);
        return $this->render('calendar/show.html.twig', [
            'room' => $room,
            'calendar' => $calendar,
            'calendarToString' => $calendarToString,
            'weeks' => $weeks,
            'firstDay' => $firstDay,
            'days' => $days,
        ]);
    }

    /**
     * @Route("/room/{id}/calendar/", name="room_calendar_next", methods="GET")
     *
     */
    public function nextMonth(Room $room, $month, $year, ReservationRepository $reservationRepository)
    {
        $calendar = new Calendar($month, $year);

        // Month and year in string
        $calendarToString = $calendar->toString();

        // number of weeks in the current month
        $weeks = $calendar->getWeeks();

        // Array on weeks days
        $days = $calendar->getDays();

        // fisrt day of the first week
        $firstDay = $calendar->getFirstDay();
        $firstDay = $firstDay->format('N') === '1' ? $firstDay : $calendar->getFirstDay()->modify('last monday');

        // last day of the last week
        $end = (clone $firstDay)->modify('+'.(6 + 7 * ($weeks - 1)). ' days');

        // Current month reservations
        $monthReservations = $reservationRepository->findBetween($firstDay, $end);
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
