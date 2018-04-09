<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 07/04/2018
 * Time: 21:06
 */

namespace App\Tests\Entity;


use App\Entity\Reservation;
use App\Entity\Room;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ReservationTest extends TestCase
{
    /** @var $reservation Reservation */
    protected  $reservation;
    /** @var $room Room */
    protected $room;
    /** @var $user User */
    protected $user;

    public function setUp()
    {
        $this->room = new Room();
        $this->user = new User();

       $this->reservation = new Reservation();
       $this->reservation->setStart(new \DateTime('2011-01-01 11:00:00'));
       $this->reservation->setEnd(new \DateTime('2011-01-01 12:00:00'));
       $this->reservation->setState(true);
       $this->reservation->setRoom($this->room);
       $this->reservation->setRoom($this->room);
       $this->reservation->setUser($this->user);
       $this->reservation->setRoom($this->room);
    }

    public function testCanBeCreate()
    {
        $this->assertInstanceOf(
            Reservation::class,
            new Reservation()
        );
    }

    public function testReservationUser()
    {
        $this->assertEquals($this->user,$this->reservation->getUser());
    }

    public function testReservationRoom()
    {
        $this->assertEquals($this->room, $this->reservation->getRoom());
    }

    public function testReservationStart()
    {
        $this->assertEquals(new \DateTime('2011-01-01 11:00:00'), $this->reservation->getStart());
    }

    public function testReservationEnd()
    {
        $this->assertEquals(new \DateTime('2011-01-01 12:00:00'), $this->reservation->getEnd());
    }

    public function testReservationState()
    {
        $this->assertEquals(true,$this->reservation->getState());
    }

}
