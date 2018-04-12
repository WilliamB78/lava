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
        $crawler = $this->client->request('GET', '/reservation/1/new/2018-04-05');
        $form = $crawler->filter('form');
        $this->assertEquals(1, $form->count());
    }

    public function testReservationNewFailed()
    {
        $this->logIn('User');
        $crawler = $this->client->request('GET', '/reservation/1/new/2018-04-05');
        $form = $crawler->filter('form')->form();
        // start
        $form['reservation[start][date][month]'] = '4';
        $form['reservation[start][date][day]'] = '5';
        $form['reservation[start][date][year]'] = '2018';
        $form['reservation[start][time][hour]'] = '11';
        $form['reservation[start][time][minute]'] = '0';
        // end
        $form['reservation[end][date][month]'] = '4';
        $form['reservation[end][date][day]'] = '5';
        $form['reservation[end][date][year]'] = '2018';
        $form['reservation[end][time][hour]'] = '10';
        $form['reservation[end][time][minute]'] = '0';

        $crawler = $this->client->submit($form);
        $errorEnd = explode(' ',$crawler->filter('#reservation_end')->attr('class'));
        $this->assertEquals(true, in_array('is-invalid', $errorEnd));
    }

    public function testReservationSuccess()
    {
        $this->logIn('Secretaire');
        $crawler = $this->client->request('GET', '/reservation/1/new/2018-04-05');
        $form = $crawler->filter('form')->form();
        // start
        $form['reservation[start][date][month]'] = '4';
        $form['reservation[start][date][day]'] = '5';
        $form['reservation[start][date][year]'] = '2018';
        $form['reservation[start][time][hour]'] = '11';
        $form['reservation[start][time][minute]'] = '0';
        // end
        $form['reservation[end][date][month]'] = '4';
        $form['reservation[end][date][day]'] = '5';
        $form['reservation[end][date][year]'] = '2018';
        $form['reservation[end][time][hour]'] = '12';
        $form['reservation[end][time][minute]'] = '0';

        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testReservationShow()
    {
        $this->logIn('Secretaire');
        $crawler = $this->client->request('GET', '/reservation/1');

        $table = $crawler->filter('table');
        $this->assertEquals(1, $table->count());
    }

    public function testReservationEdit()
    {
        $this->logIn('Secretaire');
        $crawler = $this->client->request('GET', '/reservation/1/edit');

        $titre = $crawler->filter('h1');
        $this->assertEquals('Edit Reservation', $titre->text());

        $form = $crawler->filter('form');
        $this->assertEquals(2,$form->count());
        $form = $form->first()->form();
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertEquals(200,$this->client->getResponse()->getStatusCode());
    }

    public function testDeleteReservation()
    {
        $this->logIn('Secretaire');
        $crawler = $this->client->request('GET', '/reservation/1/edit');
        $form = $crawler->filter('form')->last();
        $this->client->submit($form->form());
        $crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }


    public function setUp()
    {
        $this->client = static::createClient();
        $this->client->enableProfiler();
        $this->repository = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }
}