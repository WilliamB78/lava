<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 28/03/2018
 * Time: 20:38.
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
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $users = $manager->getRepository(User::class)->findAll();
        $rooms = $manager->getRepository(Room::class)->findAll();

        for ($i = 0; $i < 10; ++$i) {
            $reservation = new Reservation();
            $reservation->setState('created');
            $fakerDate = $faker->dateTimeThisMonth('now');
            $reservation->setDate($fakerDate->format('Y-m-d'));
            $date = $reservation->getDate();
            $start = new \DateTime($date . date('H:i', gmdate('U')));
            $end = new \DateTime($date . date('H:i', mktime(17, 30)));
            $reservation->setStart($start);
            $reservation->setEnd($end);
            // Permet de selectionner aléatoirement une room et user
            $reservation->setRoom($rooms[array_rand($rooms, 1)]);
            $reservation->setUser($users[array_rand($users, 1)]);
            $manager->persist($reservation);
        }
        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            RoomFixtures::class,
            UserFixtures::class,
        ];
    }
}
