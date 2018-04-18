<?php

namespace App\tests\testFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        // User 1
        $user1 = new User();
        $user1->setFirstname('SECRETARY');
        $user1->setLastname($faker->lastName);
        $user1->setEmail('secretaire@lava.com');
        $user1->setPassword('test');
        $user1->addRole('ROLE_SECRETARY');
        $manager->persist($user1);

        // User 2
        $user2 = new User();
        $user2->setFirstname('USER');
        $user2->setLastname($faker->lastName);
        $user2->setEmail('user@lava.com');
        $user2->setPassword('test');
        $user2->addRole('ROLE_USER');
        $manager->persist($user2);

        // User 4
        $user3 = new User();
        $user3->setFirstname('ADMIN');
        $user3->setLastname($faker->lastName);
        $user3->setEmail('admin@lava.com');
        $user3->setPassword('test');
        $user3->addRole('ROLE_ADMIN');
        $manager->persist($user3);

        $manager->flush();
    }
}
