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
        $roles = ['ROLE_SECRETARY', 'ROLE_UTILISATEUR', 'ROLE_ADMIN'];

        // On ajoute 10 room avec les fixtures
        for ($i = 0; $i < 10; ++$i) {
            //Création de la room avec faker
            $user = new User();
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setEmail($faker->email);
            $password = $this->encoder->encodePassword($user, 'test');
            $user->setPassword($password);
            $randRole = array_rand($roles, 1);
            $user->addRole($roles[$randRole]);
            $manager->persist($user);
        }

        $manager->flush();

        $this->fixturesEnvTest($manager);
    }

    private function fixturesEnvTest(ObjectManager $manager)
    {
        $user = new User();
        $user->setFirstname('user');
        $user->setLastname('user');
        $user->setEmail('user@lava.com');
        $user->setPassword($this->encoder->encodePassword($user,'user'));
        $user->addRole('ROLE_USER');
        $manager->persist($user);

        $secretaire = new User();
        $secretaire->setFirstname('secretaire');
        $secretaire->setLastname('secretaire');
        $secretaire->setEmail('secretaire@lava.com');
        $secretaire->setPassword($this->encoder->encodePassword($user,'secretaire'));
        $secretaire->addRole('ROLE_SECRETARY');
        $manager->persist($secretaire);

        $admin = new User();
        $admin->setFirstname('admin');
        $admin->setLastname('admin');
        $admin->setEmail('admin@lava.com');
        $admin->setPassword($this->encoder->encodePassword($user,'admin'));
        $admin->addRole('ROLE_ADMIN');
        $manager->persist($admin);

        $manager->flush();
    }
}
