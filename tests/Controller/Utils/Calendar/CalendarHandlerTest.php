<?php

use App\Controller\Utils\Calendar\CalendarHandler;
use App\Entity\Reservation;
use App\Entity\Room;
use App\Service\Calendar;
use App\Tests\Config\AbstractDbSetUp;
use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: hello
 * Date: 10/04/2018
 * Time: 15:16.
 */
class CalendarHandlerTest extends TestCase
{
    private $month;
    private $year;
    /** @var Calendar $calendar */
    private $calendar;

    /** @var CalendarHandler $calendarHandler */
    private $calendarHandler;

    private $em;

    /**
     * @throws Exception
     */
    public function setUp()
    {
        AbstractDbSetUp::prime();
        $this->month = 1;
        $this->year = 2018;
        $this->calendar = new Calendar($this->month, $this->year);
        $this->calendarHandler = new CalendarHandler($this->calendar);
        $this->em = AbstractDbSetUp::getEntityManager();
    }

    public function testCalendarHandlerCanBeCreated()
    {
        $this->assertInstanceOf(
            CalendarHandler::class,
            new CalendarHandler($this->calendar)
        );
    }

    public function testGetMonthToSTring()
    {
        $this->assertEquals('Janvier 2018', $this->calendarHandler->getMonthToSTring());
    }

    public function testGetWeeks()
    {
        $this->assertEquals(5, $this->calendarHandler->getWeeks());
    }

    public function testGetDays()
    {
        $this->assertInternalType('array', $this->calendarHandler->getDays());
    }

    public function testGetLastDay()
    {
        $this->assertInstanceOf('DateTime', $this->calendarHandler->getLastDay());
    }

    public function testGetMonthReservations()
    {
        $reservationRepository = $this->em->getRepository(Reservation::class);
        $room = $this->em->getRepository(Room::class)->find(1);
        $this->assertInternalType('array', $this->calendarHandler->getMonthReservations($reservationRepository, $room));
    }
}
