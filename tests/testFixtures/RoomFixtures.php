<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 28/03/2018
 * Time: 19:47.
 */

namespace App\tests\testFixtures;

use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class RoomFixtures extends Fixture
{
    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        // On ajoute 10 room avec les fixtures
        for ($i = 0; $i < 3; ++$i) {
            //Création de la room avec faker
            $room = new Room();
            $room->setName($faker->name);
            $room->setNbPlaces($faker->numberBetween(2, 15));
            $room->setState(1);
            $room->setCommentState($faker->text(150));
            $manager->persist($room);
        }

        for ($i = 0; $i < 7; ++$i) {
            //Création de la room avec faker
            $room = new Room();
            $room->setName($faker->name);
            $room->setNbPlaces($faker->numberBetween(2, 15));
            $room->setState(0);
            $room->setCommentState($faker->text(150));
            $manager->persist($room);
        }
        // On enregistre en base
        $manager->flush();
    }
}
