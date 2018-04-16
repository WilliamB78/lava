<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    /**
     * UserFixtures constructor.
     *
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $roles = ['ROLE_SECRETARY', 'ROLE_ADMIN'];

        // On ajoute 10 room avec les fixtures
        for ($i = 0; $i < 10; ++$i) {
            //Création de la room avec faker
            $user = new User();
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setEmail($faker->email);
            $password = $this->encoder->encodePassword($user, 'test');
            $user->setPassword($password);
            $user->setIsBlocked(false);
            $randRole = array_rand($roles, 1);
            $user->addRole($roles[$randRole]);
            $manager->persist($user);
        }
        for ($i = 0; $i < 4; ++$i) {
            //Création de la room avec faker
            $user1 = new User();
            $user1->setFirstname($faker->firstName);
            $user1->setLastname($faker->lastName);
            $user1->setEmail($faker->email);
            $password = $this->encoder->encodePassword($user, 'test');
            $user1->setPassword($password);
            $user1->setIsBlocked(false);
            //$randRole = array_rand($roles, 1);
            $user1->addRole('ROLE_UTILISATEUR');
            $user1->addRole('ROLE_CAN_DO_BOOKING');
            $manager->persist($user1);
        }
        $manager->flush();
    }
}
