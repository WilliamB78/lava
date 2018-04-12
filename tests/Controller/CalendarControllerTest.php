<?php
/**
 * Created by PhpStorm.
 * User: hello
 * Date: 12/04/2018
 * Time: 11:45
 */

namespace App\Tests\Controller;


use App\Controller\Utils\Calendar\CalendarHandler;
use App\Entity\Reservation;
use App\Service\Calendar;
use App\Tests\Config\AbstractDbSetUp;
use App\Tests\Traits\UserLogger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class CalendarControllerTest extends WebTestCase
{
    use UserLogger;

    /** @var Client $client */
    private $client;

    /** @var $repository */
    private $repository;

    private $month;

    private $year;

    private $calendar;

    /** @var CalendarHandler $calendarHandler */
    private $calendarHandler;

    /**
     * @throws \Exception
     */
    public function setUp()
    {
        $this->client = static::createClient();
        $this->repository = AbstractDbSetUp::getEntityManager();
        $this->month = 1;
        $this->year = 2018;
        $this->calendar = new Calendar($this->month, $this->year);
        $this->calendarHandler = new CalendarHandler($this->calendar);
    }

    public function testGetRouteShowIfNotAuthenticate()
    {
        $this->client->request(Request::METHOD_GET, '/room/1/calendar');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testGetRouteShowIfAuthenticate()
    {
        $this->logIn('User');
        $this->client->request(Request::METHOD_GET, '/room/1/calendar');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}