<?php
/**
 * Created by PhpStorm.
 * User: hello
 * Date: 11/04/2018
 * Time: 14:11
 */

namespace App\Tests\Controller;



use App\Entity\Room;
use App\Entity\User;
use App\Tests\Config\AbstractDbSetUp;
use App\Tests\Traits\UserLogger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    use UserLogger;

    /** @var Client $client  */
    private $client;

    /** @var $repository */
    private $repository;

    public function setUp(){
        $this->client = static::createClient();
        $this->repository = AbstractDbSetUp::getEntityManager();
    }

    public function testIndexShouldHaveH1(){
        $crawler = $this->goToIndex();

        $heading = $crawler->filter('h1')->eq(0)->text();

        $this->assertEquals('Liste des utilisateurs', $heading);
    }

    public function testIndexShouldHave3User(){
        $crawler = $this->goToIndex();

        $users = $this->getUsers();

        $this->assertCount(3, $users);

    }

    public function testIndexShouldHaveRoomArray(){
        $crawler = $this->goToIndex();

        $rooms = $this->getUsers();

        $this->assertInternalType('array', $rooms);
    }


    public function testNewUser(){
        $this->logIn('Admin');
        $crawler = $this->client->request('GET', '/user/new');
        // enables the profiler for the next request (it does nothing if the profiler is not available)
        $this->client->enableProfiler();

        // select the form and fill in some values
        $form = $crawler->filter('.user_new')->form();
        $form['user[firstname]'] = 'symfonyfan';
        $form['user[lastname]'] = 'symfonyfan';
        $form['user[email]'] = 'symfonyfan@sf.com';
        $form['user[password]'] = 'test';
        $form['user[roles]'] = 'ROLE_UTILISATEUR';

        // submits the given form
        $crawler = $this->client->submit($form);

        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');

        // checks that an email was sent
        $this->assertSame(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertSame('[admin@lava.com] Lava Booking System Account is Active', $message->getSubject());
        $this->assertSame('admin@lava.com', key($message->getFrom()));
        $this->assertSame('symfonyfan@sf.com', key($message->getTo()));
    }

    public function testShowUserDetail(){
        $this->logIn('Admin');
        $user = $this->getUsers();
        $crawler = $this->client->request('GET', '/user/'.$user[0]->getId());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertInstanceOf('App\Entity\User', $user[0]);
    }

    public function goToIndex(){
        $this->logIn('Admin');
        return $crawler = $this->client->request('GET', '/user/');
    }

    public function getUsers(){
        return $this->repository->getRepository(User::class)->findAll();
    }
}