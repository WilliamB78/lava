<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 28/03/2018
 * Time: 20:38
 */

namespace App\DataFixtures;


use App\Entity\Reservation;
use App\Entity\Room;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class ReservationFixtures extends Fixture implements DependentFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $user = new User();
        $user->setFirstname($faker->firstName);
        $user->setLastname($faker->lastName);
        $user->setEmail($faker->companyEmail);
        $user->setPassword($faker->password);
        $manager->persist($user);
        $manager->flush();
        #$users = $manager->getRepository(User::class)->findAll();
        $rooms = $manager->getRepository(Room::class)->findAll();
        for($i = 0; $i < 10; $i++) {
            $reservation = new Reservation();
            $reservation->setState('created');
            $reservation->setSlot($faker->dateTime());
            # Permet de selectionner aléatoirement une room et user
            $reservation->setRoom($rooms[array_rand($rooms,1)]);
            $reservation->setUser($user);
            $manager->persist($reservation);
        }
        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    function getDependencies()
    {
        return [
            RoomFixtures::class
        ];
    }
}