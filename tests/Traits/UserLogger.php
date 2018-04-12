<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 10/04/2018
 * Time: 11:51
 */

namespace App\Tests\Traits;


use App\Entity\User;
use App\Tests\Config\AbstractDbSetUp;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


trait UserLogger
{
    /** @var Client $client */
    private $client;
    private $doctrine;

    private function logIn($role)
    {
        AbstractDbSetUp::prime();
        /** @var Session $session */
        $session = AbstractDbSetUp::getSession();
        if ($role == 'User') {
            $user = $this->getUser();
        }
        if ($role == 'Secretaire') {
            $user = $this->getSecretaire();
        }
        if ($role == 'Admin') {
            $user = $this->getAdmin();
        }
        $firewallContext = 'main';

        $token = new UsernamePasswordToken($user, null, 'user_provider', $user->getRoles());
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    private function getUser(): User
    {
        $doctrine = AbstractDbSetUp::getEntityManager();

        return $doctrine->getRepository(User::class)->findOneBy(['firstname' => 'USER']);


    }

    private function getSecretaire(): User
    {
        $doctrine = AbstractDbSetUp::getEntityManager();

        return $doctrine->getRepository(User::class)->findOneBy(array('firstname' => 'SECRETARY'));
    }

    private function getAdmin(): User
    {
        $doctrine = AbstractDbSetUp::getEntityManager();
        //dump($doctrine->getRepository(User::class)->findByFirstname('ADMIN'));

        return $doctrine->getRepository(User::class)->findOneBy(array('firstname' => 'ADMIN'));
    }

}