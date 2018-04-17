<?php
/**
 * Created by PhpStorm.
 * User: bmnk
 * Date: 04/04/18
 * Time: 21:44.
 */

namespace App\Controller;

use App\Controller\Utils\Calendar\CalendarHandler;
use App\Entity\Room;
use App\Repository\ReservationRepository;
use App\Service\Calendar;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/room", name="room_")
 */
class CalendarController extends Controller
{
    /**
     * @Route("/{id}/calendar", name="calendar_show", methods="GET")
     * @Security("has_role('ROLE_UTILISATEUR') or is_granted('ROLE_SECRETARY')")
     *
     * @param Room                  $room
     * @param Calendar              $calendar
     * @param ReservationRepository $reservationRepository
     *
     * @return Response
     */
    public function show(Room $room, Calendar $calendar, ReservationRepository $reservationRepository): Response
    {
        dump($this->getUser());
        $handler = new CalendarHandler($calendar);

        // Month and year in string
        $calendarToString = $handler->getMonthToSTring();

        // number of weeks in the current month
        $weeks = $handler->getWeeks();

        // Array on weeks days
        $days = $handler->getDays();

        // fisrt day of the first week
        $firstDay = $handler->getFirstDay();

        // Current month reservations
        $monthReservationsByDay = $handler->getMonthReservations($reservationRepository, $room);

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
     * @Route("/{id}/calendar/{month}/{year}", name="calendar_previous", methods="GET")
     * @Security("has_role('ROLE_UTILISATEUR') or is_granted('ROLE_SECRETARY')")
     *
     * @param Room $room
     * @param $month
     * @param $year
     * @param ReservationRepository $reservationRepository
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function previousMonth(Room $room, $month, $year, ReservationRepository $reservationRepository)
    {
        $calendar = new Calendar($month, $year);

        $handler = new CalendarHandler($calendar);

        // Month and year in string
        $calendarToString = $handler->getMonthToSTring();

        // number of weeks in the current month
        $weeks = $handler->getWeeks();

        // Array on weeks days
        $days = $handler->getDays();

        // fisrt day of the first week
        $firstDay = $handler->getFirstDay();

        // Current month reservations
        $monthReservationsByDay = $handler->getMonthReservations($reservationRepository, $room);

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
     * @Route("/{id}/calendar/", name="calendar_next", methods="GET")
     * @Security("has_role('ROLE_UTILISATEUR') or is_granted('ROLE_SECRETARY')")
     *
     * @param Room $room
     * @param $month
     * @param $year
     * @param ReservationRepository $reservationRepository
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function nextMonth(Room $room, $month, $year, ReservationRepository $reservationRepository)
    {
        $calendar = new Calendar($month, $year);

        $handler = new CalendarHandler($calendar);

        // Month and year in string
        $calendarToString = $handler->getMonthToSTring();

        // number of weeks in the current month
        $weeks = $handler->getWeeks();

        // Array on weeks days
        $days = $handler->getDays();

        // fisrt day of the first week
        $firstDay = $handler->getFirstDay();

        // Current month reservations
        $monthReservationsByDay = $handler->getMonthReservations($reservationRepository, $room);

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
}
