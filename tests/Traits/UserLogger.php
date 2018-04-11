<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 10/04/2018
 * Time: 11:51
 */

namespace App\Tests\Traits;


use App\Entity\User;
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
        /** @var Session $session */
        $session = $this->client->getContainer()->get('session');
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
        $doctrine = $this->client->getContainer()->get('doctrine');

        return $doctrine->getRepository(User::class)->findOneBy(['email' => 'user@lava.com']);


    }

    private function getSecretaire(): User
    {
        $doctrine = $this->client->getContainer()->get('doctrine');

        return $doctrine->getRepository(User::class)->findOneBy(['email' => 'secretaire@lava.com']);
    }

    private function getAdmin(): User
    {
        $doctrine = $this->client->getContainer()->get('doctrine');

        return $doctrine->getRepository(User::class)->findOneBy(['email' => 'admin@lava.com']);
    }

}