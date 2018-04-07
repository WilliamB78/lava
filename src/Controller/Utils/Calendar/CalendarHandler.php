<?php
/**
 * Created by PhpStorm.
 * User: bmnk
 * Date: 07/04/18
 * Time: 13:21.
 */

namespace App\Controller\Utils\Calendar;

use App\Repository\ReservationRepository;
use App\Service\Calendar;

class CalendarHandler
{
    /** @var Calendar $calendar */
    private $calendar;

    /**
     * CalendarHandler constructor.
     *
     * @param Calendar $calendar
     */
    public function __construct(Calendar $calendar)
    {
        $this->calendar = $calendar;
    }

    /**
     * @return string
     */
    public function getMonthToSTring()
    {
        return $this->calendar->toString();
    }

    /**
     * @return int
     */
    public function getWeeks()
    {
        return $this->calendar->getWeeks();
    }

    /**
     * @return array
     */
    public function getDays()
    {
        return $this->calendar->getDays();
    }

    /**
     * @return \DateTime|static
     */
    public function getFirstDay()
    {
        $firstDay = $this->calendar->getFirstDay();
        $firstDay = '1' === $firstDay->format('N') ? $firstDay : $this->calendar->getFirstDay()->modify('last monday');

        return $firstDay;
    }

    /**
     * @return static
     */
    public function getLastDay()
    {
        $firstDay = $this->getFirstDay();
        $weeks = $this->getWeeks();

        return (clone $firstDay)->modify('+'.(6 + 7 * ($weeks - 1)).' days');
    }

    /**
     * @param ReservationRepository $reservationRepository
     * @param $room
     *
     * @return array
     */
    public function getMonthReservations(ReservationRepository $reservationRepository, $room)
    {
        $firstDay = $this->getFirstDay();
        $end = $this->getLastDay();

        /** @var Calendar $reservations */
        $reservations = $reservationRepository->findBetween($firstDay, $end, $room->getId());

        $monthReservationsByDay = [];

        foreach ($reservations as $reservation) {
            $date = $reservation->getStart()->format('Y-m-d');
            if (!isset($monthReservationsByDay[$date])) {
                $monthReservationsByDay[$date] = [$reservation];
            } else {
                $monthReservationsByDay[$date][] = $reservation;
            }
        }

        return $monthReservationsByDay;
    }
}
