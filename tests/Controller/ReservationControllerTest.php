<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 09/04/2018
 * Time: 14:18.
 */

namespace App\Tests\Controller;

use App\Entity\Reservation;
use App\Tests\Traits\UserLogger;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReservationControllerTest extends WebTestCase
{
    use UserLogger;
    /** @var Client $client */
    private $client = null;
    /** @var EntityManager $repository */
    private $repository;

    /**
     * Test qu'un utilisateur n'ai pas le droit d'accès sur cette page.
     */
    public function testReservationIndexUser()
    {
        $this->logIn('User');
        $this->client->request('GET', '/reservation/');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test qu'une secrétaire ai accès correctement à la liste des réservations.
     */
    public function testReservationIndexSecretaire()
    {
        $this->logIn('Secretaire');
        $crawler = $this->client->request('GET', '/reservation/');
        $title = $crawler->filter('title')->text();
        $this->assertEquals('Liste des réservations', $title);
    }

    /**
     * Test qu'un utilisateur ai bien accès à la liste de ses réservations.
     */
    public function testReservationMesReservations()
    {
        $this->logIn('User');
        $crawler = $this->client->request('GET', '/reservation/mes-reservations');
        $title = $crawler->filter('title');
        $this->assertEquals('Mes réservations', $title->text());
    }

    public function testReservationNewLoaded()
    {
        $this->logIn('User');
        $crawler = $this->client->request('GET', '/reservation/5/new/2018-04-05');
        $form = $crawler->filter('.reservation_new');
        $this->assertEquals(1, $form->count());
    }

    public function testReservationNewFailed()
    {
        $this->logIn('Admin');
        $crawler = $this->client->request('GET', '/reservation/5/new/2018-04-05');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testReservationSuccess()
    {
        $this->logIn('User');
        $crawler = $this->client->request('GET', '/reservation/1/new/2018-04-18');
        $form = $crawler->filter('form')->form();
        // date
        $form['reservation[date]'] = '2018-04-30';
        // start
        $form['reservation[start]'] = '08:00';
        // end
        $form['reservation[end]'] = '17:00';

        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testReservationShow()
    {
        $this->logIn('Secretaire');
        $crawler = $this->client->request('GET', '/reservation/1');

        $card = $crawler->filter('.card');
        $this->assertEquals(1, $card->count());
    }

//    public function testReservationEdit()
//    {
//        $reservation = $this->repository->getRepository(Reservation::class)->findOneBy(['user' => 1]);
//        $user = $reservation->getUser();
//        $this->logIn(null, $user->getId());
//
//        $crawler = $this->client->request('GET', '/reservation/'.$reservation->getId().'/edit');
//
//        $titre = $crawler->filter('title');
//        $this->assertEquals('Edition réservation', $titre->text());
//
//        $form = $crawler->filter('form');
//        $this->assertEquals(1, $form->count());
//
//        $form = $form->first()->form();
//
//        // date
//        $form['reservation[date]'] = '2018-04-30';
//        // start
//        $form['reservation[start]'] = '08:00';
//        // end
//        $form['reservation[end]'] = '17:00';
//
//        $this->client->submit($form);
//        $this->client->followRedirect();
//        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
//    }

    public function testDeleteReservation()
    {
        $reservation = $this->repository->getRepository(Reservation::class)->findOneBy(['user' => 1]);
        $user = $reservation->getUser();
        $this->logIn(null, $user->getId());
        $this->client->request('DELETE', '/reservation/'.$reservation->getId());
        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function setUp()
    {
        /* @var Client client */
        $this->client = static::createClient();
        $this->repository = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }
}
