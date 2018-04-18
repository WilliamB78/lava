<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 11/04/2018
 * Time: 14:23.
 */

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\Config\AbstractDbSetUp;
use App\Tests\Traits\UserLogger;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class SecurityControllerTest extends WebTestCase
{
    use UserLogger;

    /** @var Client $client */
    private $client = null;
    /** @var EntityManager $repository */
    private $repository;

    public function testLogin()
    {
        $crawler = $this->client->request('GET', '/');
        $sign = $crawler->filter('h1');
        $this->assertEquals(1, $sign->count());
        $this->assertEquals('Sign in', $sign->text());
    }

    public function testForgotPassword()
    {
        $crawler = $this->client->request('GET', '/forgot-password');
        $form = $crawler->filter('form');
        $this->assertEquals(1, $form->count());
    }

    public function testForgotPasswordFailed()
    {
        $crawler = $this->client->request('GET', '/forgot-password');
        $form = $crawler->filter('form')->form();
        $form['forgot_password[email]'] = 'testfailed@lava.com';
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $error = $crawler->filter('div.alert-danger');
        $this->assertEquals(1, $error->count());
    }

    public function testForgotPasswordSuccess()
    {
        $crawler = $this->client->request('GET', '/forgot-password');

        $this->client->enableProfiler();
        $form = $crawler->filter('form')->form();
        $form['forgot_password[email]'] = 'user@lava.com';
        $this->client->submit($form);
        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');

        // checks that an email was sent
        $this->assertSame(1, $mailCollector->getMessageCount());
        $crawler = $this->client->followRedirect();
        $error = $crawler->filter('div.alert-success');
        $this->assertEquals(1, $error->count());
    }

    /**
     * @throws \Exception
     */
    public function testResetPasswordTokenExpired()
    {
        /** @var User $user */
        $user = $this->repository->getRepository(User::class)->findOneBy(['email' => 'user@lava.com']);
        //dump($user);
        $date = new \DateTime();
        $date->sub(new \DateInterval('P10D'));
        $user->setTokenExpire($date);
        $this->client->request('GET', '/reset-password/'.$user->getTokenResetPassword());

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testResetPasswordLoaded()
    {
        /** @var User $user */
        $user = $this->repository->getRepository(User::class)->findOneBy(['email' => 'user@lava.com']);
        $crawler = $this->client->request('GET', '/reset-password/'.$user->getTokenResetPassword());
        $form = $crawler->filter('form');
        $this->assertEquals(1, $form->count());
        $form = $crawler->filter('form')->form();
        $form['reset_password[password][first]'] = 'test';
        $form['reset_password[password][second]'] = 'test';
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function setUp()
    {
        $this->client = static::createClient();
        $this->repository = AbstractDbSetUp::getEntityManager();
    }
}
