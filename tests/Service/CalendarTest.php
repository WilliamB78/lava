<?php
/**
 * Created by PhpStorm.
 * User: hello
 * Date: 10/04/2018
 * Time: 16:43.
 */

namespace App\tests\Service;

use App\Service\Calendar;
use App\Tests\Config\AbstractDbSetUp;
use PHPUnit\Framework\TestCase;

class CalendarTest extends TestCase
{
    private $month;
    private $year;
    /** @var Calendar $calendar */
    private $calendar;

    /**
     * @throws \Exception
     */
    public function setUp()
    {
        AbstractDbSetUp::prime();

        $this->em = AbstractDbSetUp::getEntityManager();
    }

    /**
     * @throws \Exception
     */
    public function testCalendarCanBeCreated()
    {
        $this->assertInstanceOf(
            Calendar::class,
            new Calendar($this->month, $this->year)
        );
    }

    /**
     * @throws \Exception
     */
    public function testInThisMonth()
    {
        $this->month = date('n');
        $this->year = date('Y');
        $this->calendar = new Calendar($this->month, $this->year);

        $date = new \DateTime();

        $this->assertNotFalse($this->calendar->inThisMonth($date));
    }

    /**
     * @throws \Exception
     */
    public function testGetWeeks()
    {
        $this->calendar = new Calendar($this->month, $this->year);
        $this->assertInternalType('int', $this->calendar->getWeeks());
    }

    /**
     * @throws \Exception
     */
    public function testGetMonth()
    {
        $this->calendar = new Calendar($this->month, $this->year);
        $this->assertInternalType('int', $this->calendar->getMonth());
    }

    /**
     * @throws \Exception
     */
    public function testGetFirstDay()
    {
        $this->calendar = new Calendar($this->month, $this->year);
        $this->assertInstanceOf('Datetime', $this->calendar->getFirstDay());
    }

    /**
     * @throws \Exception
     */
    public function testPreviousMonth()
    {
        $this->calendar = new Calendar($this->month + 1, $this->year + 1);
        $this->assertInstanceOf(
            Calendar::class,
            $this->calendar->previousMonth()
        );
    }

    /**
     * @throws \Exception
     */
    public function testNextMonth()
    {
        $this->calendar = new Calendar($this->month - 1, $this->year - 1);
        $this->assertInstanceOf(
            Calendar::class,
            $this->calendar->nextMonth()
        );
    }
}
