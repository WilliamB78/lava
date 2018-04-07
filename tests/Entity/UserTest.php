<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 07/04/2018
 * Time: 18:59
 */

namespace App\Tests\Entity;

use App\Entity\Reservation;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{

    public function testUserCanBeCreate()
    {
        $this->assertInstanceOf(
            User::class,
            new User()
        );
    }

    public function testUserHasFirstName()
    {
        $user = new User();
        $user->setFirstname('alexis');

        $this->assertEquals('alexis', $user->getFirstname());
    }

    public function testUserHasLastName()
    {
        $user = new User();
        $user->setLastname('smith');

        $this->assertEquals('smith', $user->getLastname());
    }

    public function testUserHasEmail()
    {
        $user = new User();
        $user->setEmail('test@test.com');

        $this->assertEquals('test@test.com', $user->getEmail());
    }

    public function testUserHasPassword()
    {
        $user = new User();
        $user->setPassword('test');

        $this->assertEquals('test', $user->getPassword());
    }

    public function testUserHasRole()
    {
        $user = new User();
        $user->setRoles('role_utilisateur');

        $this->assertContains('role_utilisateur', $user->getRoles());
    }

    public function testUserHasReservation()
    {
        $user = new User();
        $reservation = new Reservation();

        $user->addReservations($reservation);

        $this->assertContains($reservation,$user->getReservations());// aps sur ici
    }

    public function testUserHasToken()
    {
        $user = new User();
        $user->setTokenResetPassword('monsupertoken');

        $this->assertEquals('monsupertoken', $user->getTokenResetPassword());
    }

    public function testUserHasTokenExpire()
    {
        $user = new User();
        $user->setTokenExpire(new \DateTime('2011-01-01'));

        $this->assertEquals(new \DateTime('2011-01-01'), $user->getTokenExpire());
    }

}