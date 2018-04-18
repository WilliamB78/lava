<?php
/**
 * Created by PhpStorm.
 * User: hello
 * Date: 10/04/2018
 * Time: 11:03.
 */

namespace App\Tests\Repository;

use App\Entity\Reservation;
use App\Entity\User;
use App\Tests\Config\AbstractDbSetUp;
use PHPUnit\Framework\TestCase;

class ReservationRepositoryTest extends TestCase
{
    private $em;

    public function setUp()
    {
        AbstractDbSetUp::prime();
        $this->em = AbstractDbSetUp::getEntityManager();
    }

    public function testFindUserReversationByState()
    {
        $user = $this->em->getRepository(Reservation::class)->find(1);
        $state = 'created';
        $repository = $this->em->getRepository(Reservation::class);

        $totalReservationForUSer = $repository->findUserReversationByState($user, $state);

        $this->assertEquals($totalReservationForUSer, $repository->findUserReversationByState($user, $state));
    }

    public function testFindByState()
    {
        $state = 'refused';
        $repository = $this->em->getRepository(Reservation::class);
        $this->assertEquals(0, $repository->findByState($state));
    }

    public function testByState()
    {
        $state = 'created';
        $repository = $this->em->getRepository(Reservation::class);
        $count = $repository->countByState($state);
        $this->assertEquals(10, $repository->countByState($state));
    }
}
