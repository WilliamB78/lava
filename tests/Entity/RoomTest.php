<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 07/04/2018
 * Time: 20:36.
 */

namespace App\Tests\Entity;

use App\Entity\Reservation;
use App\Entity\Room;
use PHPUnit\Framework\TestCase;

class RoomTest extends TestCase
{
    public function testRoomCanBeCreate()
    {
        $this->assertInstanceOf(
            Room::class,
            new Room()
        );
    }

    public function testRoomHasName()
    {
        $room = new Room();
        $room->setName('name');

        $this->assertEquals('name', $room->getName());
    }

    public function testRoomHasNbPlace()
    {
        $room = new Room();
        $room->setNbPlaces(3);

        $this->assertEquals(3, $room->getNbPlaces());
    }

    public function testRoomHasState()
    {
        $room = new Room();
        $room->setState(true);

        $this->assertEquals(true, $room->getState());
    }

    public function testRoomHasCommentState()
    {
        $room = new Room();
        $room->setCommentState('comment state');

        $this->assertEquals('comment state', $room->getCommentState());
    }

    public function testRoomHasReservation()
    {
        $room = new Room();
        $reservation = new Reservation();

        $room->addReservation($reservation);

        $this->assertContains($reservation, $room->getReservations());
    }
}
