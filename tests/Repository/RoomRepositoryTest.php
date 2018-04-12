<?php
/**
 * Created by PhpStorm.
 * User: bmnk
 * Date: 09/04/18
 * Time: 18:56
 */

namespace App\Tests\Repository;


use App\Entity\Room;
use App\Tests\Config\AbstractDbSetUp;
use PHPUnit\Framework\TestCase;

class RoomRepositoryTest extends TestCase
{
    private $em;


    public function setUp()
    {
        AbstractDbSetUp::prime();
        $this->em = AbstractDbSetUp::getEntityManager();
    }


    /**
     * @throws \Exception
     */
    public function testTotalRoom()
    {
        $repository = $this->em->getRepository(Room::class);
        $this->assertEquals(10, $repository->findTotalRoom());
    }

    /**
     * @throws \Exception
     */
    public function testOpenRoom()
    {
        $repository = $this->em->getRepository(Room::class);
        $this->assertCount(7, $repository->findTotalRoomOpen());
    }

    /**
     * @throws \Exception
     */
    public function testClosedRoom()
    {
        $repository = $this->em->getRepository(Room::class);
        $this->assertCount(3, $repository->findTotalRoomClosed());
    }
}