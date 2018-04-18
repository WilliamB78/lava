<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 09/04/2018
 * Time: 14:18
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
    /** @var Client $client  */
    private $client = null;
    /** @var EntityManager $repository */
    private $repository;


    /**
     * Test qu'un utilisateur n'ai pas le droit d'accès sur cette page
     */
    public function testReservationIndexUser()
    {
        $this->logIn('User');
        $this->client->request('GET', '/reservation/');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test qu'une secrétaire ai accès correctement à la liste des réservations
     */
    public function testReservationIndexSecretaire()
    {
        $this->logIn("Secretaire");
        $crawler = $this->client->request("GET", "/reservation/");
        $title = $crawler->filter('title')->text();
        $this->assertEquals("Liste des réservations", $title);
    }

    /**
     * Test qu'un utilisateur ai bien accès à la liste de ses réservations
     */
    public function testReservationMesReservations()
    {
        $this->logIn("User");
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
// TODO refaire ce test
//    public function testReservationNewFailed()
//    {
//        $this->logIn('User');
//        $crawler = $this->client->request('GET', '/reservation/5/new/2018-04-05');
//        $form = $crawler->filter('.reservation_new')->form();
//        // start
//        $form['reservation[start]'] = "2018-04-11 08:00";
//        // end
//        $form['reservation[end]'] = "2018-04-19 08:00";
//        dump($form);
//        $crawler = $this->client->submit($form);
//        $errorEnd = explode(' ',$crawler->filter('#reservation_end')->attr('class'));
//        $this->assertEquals(true, in_array('is-invalid', $errorEnd));
//    }

    public function testReservationSuccess()
    {
        $this->logIn('User');
        $crawler = $this->client->request('GET', '/reservation/1/new/2018-04-05');
        $form = $crawler->filter('form')->form();
        // date
        $form['reservation[date]'] = "2018-04-18";
        // start
        $form['reservation[start]'] = "08:00";
        // end
        $form['reservation[end]'] = "17:00";

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

// TODO refaire ce test le resultat est un exeption?? chercher la cause
    public function testReservationEdit()
    {
        $user = $this->repository->getRepository(Reservation::class)->find(5)->getUser();
        $this->logIn(null,$user->getId());

        $crawler = $this->client->request('GET', '/reservation/5/edit');

        $titre = $crawler->filter('title');
        $this->assertEquals('Edition réservation', $titre->text());

        $form = $crawler->filter('form');
        $this->assertEquals(1,$form->count());

        $form = $form->first()->form();

        // date
        $form['reservation[date]'] = "2018-04-18";
        // start
        $form['reservation[start]'] = "08:00";
        // end
        $form['reservation[end]'] = "17:00";

        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertEquals(200,$this->client->getResponse()->getStatusCode());
    }

// TODO refaire ce test
    /*public function testDeleteReservation()
    {
        $this->logIn('Admin');
        $crawler = $this->client->request('DELETE', '/reservation/5/delete');
        $form = $crawler->filter('form')->form();
        dump($form);
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }*/


    public function setUp()
    {
        /** @var Client client */
        $this->client = static::createClient();
        $this->repository = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }
}