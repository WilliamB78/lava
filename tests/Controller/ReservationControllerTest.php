<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 09/04/2018
 * Time: 14:18
 */

namespace App\Tests\Controller;


use App\Tests\Traits\UserLogger;
use Doctrine\ORM\EntityManager;
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
        // start
        $form['reservation[start]'] = "2018-04-18 08:00";
//        $form['reservation[start][date][month]'] = '4';
//        $form['reservation[start][date][day]'] = '5';
//        $form['reservation[start][date][year]'] = '2018';
//        $form['reservation[start][time][hour]'] = '11';
//        $form['reservation[start][time][minute]'] = '0';
        // end
        $form['reservation[end]'] = "2018-04-19 08:00";
//        $form['reservation[end][date][month]'] = '4';
//        $form['reservation[end][date][day]'] = '5';
//        $form['reservation[end][date][year]'] = '2018';
//        $form['reservation[end][time][hour]'] = '12';
//        $form['reservation[end][time][minute]'] = '0';

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
    //    public function testReservationEdit()
//    {
//        $this->logIn('User');
//        $crawler = $this->client->request('GET', '/reservation/5/edit');
//
//        $titre = $crawler->filter('h1');
//        $this->assertEquals('Edit Reservation', $titre->text());
//
//        $form = $crawler->filter('form');
//        $this->assertEquals(2,$form->count());
//        $form = $form->first()->form();
//        $this->client->submit($form);
//        $this->client->followRedirect();
//        $this->assertEquals(200,$this->client->getResponse()->getStatusCode());
//    }

// TODO refaire ce test
//    public function testDeleteReservation()
//    {
//        $this->logIn('Secretary');
//        $crawler = $this->client->request('DELETE', '/reservation/5/delete');
//        $form = $crawler->filter('form')->form();
//        dump($form);
//        $this->client->submit($form);
//        $crawler = $this->client->followRedirect();
//        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
//    }


    public function setUp()
    {
        $this->client = static::createClient();
        //$this->client->enableProfiler();
        $this->repository = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }
}