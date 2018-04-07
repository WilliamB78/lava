<?php
/**
 * Created by PhpStorm.
 * User: hello
 * Date: 06/04/2018
 * Time: 12:28.
 */

namespace App\Twig;

use App\Entity\Room;
use App\Service\Calendar;
use Doctrine\ORM\EntityManager;

class AppRuntime
{
    /** @var EntityManager $em */
    protected $em;

    /**
     * AppRuntime constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $state
     *
     * @return string
     */
    public function statesSwitcher($state)
    {
        if ('created' == $state) {
            $result = 'En attente du secretariat';
        }
        if ('accepted' == $state) {
            $result = 'Accepté';
        }
        if ('refused' == $state) {
            $result = 'Refusé';
        }
        if ('cancelled' == $state) {
            $result = 'Annulé';
        }
        if ('cancelled_ok' == $state) {
            $result = 'Supprimé';
        }

        return $result;
    }

    /**
     * @param $variable
     *
     * @return mixed
     */
    public function cloneVar($variable)
    {
        return clone $variable;
    }

    /**
     * @param $value
     * @param $iterationValue
     *
     * @return mixed
     */
    public function dayNextValue($value, $iterationValue)
    {
        return $value + $iterationValue * 7;
    }

    /**
     * @param $monthReservation
     * @param $day
     *
     * @return array
     */
    public function dayReservations($monthReservation, $day)
    {
        if (isset($monthReservation[$day])) {
            return $monthReservation[$day];
        } else {
            return [];
        }
    }

    /**
     * @param Calendar $calendar
     * @param $day
     *
     * @return mixed
     */
    public function dayInTheMonth($calendar, $day)
    {
        return $calendar->inThisMonth($day);
    }

    public function getRoomName($roomId)
    {
        $room = $this->em
            ->getRepository(Room::class)
            ->find($roomId);

        return $room->getName();
    }
}
