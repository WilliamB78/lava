<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    /**
     * UserFixtures constructor.
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
        $roles = ['ROLE_SECRETARY', 'ROLE_USER', 'ROLE_ADMIN'];

        # On ajoute 10 room avec les fixtures
        for($i = 0; $i < 10; $i++) {
            #Création de la room avec faker
            $user = new User();
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setEmail($faker->email);
            $password = $this->encoder->encodePassword($user, 'test');
            $user->setPassword($password);
            $randRole = array_rand($roles, 1);
            $user->setRole($roles[$randRole]);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
