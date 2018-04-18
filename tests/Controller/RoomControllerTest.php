<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 09/04/2018
 * Time: 14:18
 */

namespace App\Tests\Controller;

use App\Entity\Room;
use App\Tests\Traits\UserLogger;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;

class RoomControllerTest extends WebTestCase
{
    use UserLogger;

    /** @var Client $client  */
    private $client = null;
    /** @var EntityManager $repository */
    private $repository;

    /**
     * Test de la liste des salles disponible
     */
    public function testIndex()
    {
        $this->logIn('Secretaire');

        $crawler = $this->client->request('GET', '/room/');

        $reservations = $this->repository->getRepository(Room::class)->findAll();

        $this->assertCount(10, $reservations);
        $this->assertInternalType('array', $reservations);
    }

    /**
     * Test bouton calendrier
     */
    public function testIndexCalendar()
    {
        $this->logIn('Secretaire');

        $crawler = $this->client->request('GET', '/room/');
        $calendar = $crawler->filter('.btn-info');
        $this->assertEquals("Calendrier", $calendar->text());

        $this->client->click($calendar->link());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

    }

    /**
     * Test bouton indisponible
     */
    public function testIndexBtnIndispo()
    {
        $this->logIn('Secretaire');

        $crawler = $this->client->request('GET', '/room/');
        $indispo = $crawler->filter('.btn-warning');

        $this->assertEquals("Rendre indispo.", $indispo->text());

        $this->client->click($indispo->link());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test bouton disponible dans les salle hors service
     */
    public function testIndexBtnDispo()
    {
        $this->logIn('Secretaire');

        $crawler = $this->client->request('GET', '/room/hors_service');
        $dispo = $crawler->filter('.btn-warning');
        $this->assertEquals("Rendre dispo.", $dispo->text());

        $this->client->click($dispo->link());
        $this->assertEquals(200,$this->client->getResponse()->getStatusCode());
    }

    /**
     * Test le bon chargement du formulaire d'ajout d'une room
     */
    public function testNewRoomLoaded()
    {
        $this->logIn('Secretaire');
        $crawler = $this->client->request('GET', '/room/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals(1, $crawler->filter('form')->count());
    }

    /**
     * Test la validation du formulaire invalide
     */
    public function testNewRoomFailed()
    {
        $this->logIn('Secretaire');
        $crawler = $this->client->request('GET', '/room/new');

        $form = $crawler->filter('form')->form();
        $form['room[name]'] = '';
        $form['room[nbPlaces]'] = -12;

        $crawler = $this->client->submit($form);

        $errorName = explode(' ',$crawler->filter('#room_name')->attr('class'));
        $errorNbPlace = explode(' ',$crawler->filter('#room_nbPlaces')->attr('class'));

        $this->assertEquals(true, in_array('is-invalid', $errorName));
        $this->assertEquals(true, in_array('is-invalid', $errorNbPlace));
    }

    /**
     * Test la validation d'un formulaire valide
     */
    public function testNewRoomSuccess()
    {
        $this->logIn('Secretaire');
        $crawler = $this->client->request('GET', '/room/new');

        $form = $crawler->filter('form')->form();
        $form['room[name]'] = 'test';
        $form['room[nbPlaces]'] = 12;

        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test l'acces a une room pour un user
     */
    public function testShowRoomAsUser()
    {
        $this->logIn('User');
        $this->client->request('GET', '/room/1');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test l'acces et la bonne vue d'une room pour une secretaire
     */
    public function testShowRoomAsSecretary()
    {
        $this->logIn('Secretaire');
        $crawler = $this->client->request('GET', '/room/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $room = $this->repository->getRepository(Room::class)->find(1);
        $title = $crawler->filter('title')->text();
        $this->assertEquals('Salle '.$room->getName(), $title);
    }

    /**
     * Test la redirection pour un user qui souhaite modifier une salle
     */
    public function testEditRoomAsUser()
    {
        $this->logIn('Admin');
        $this->client->request('GET', '/room/1/edit');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test l'acces et la bonne vue du formulaire d'edition pour une secretaire
     */
    public function testEditRoomAsSecretary()
    {
        $this->logIn('Secretaire');
        $crawler = $this->client->request('GET', '/room/1/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        // On va chercher le premier enregistrement
        $room = $this->repository->getRepository(Room::class)->find(1);
        $title = $crawler->filter('title')->text();
        $this->assertEquals('Edition '.$room->getName(), $title);
        // On vérifie que le formulaire est bien présent
        $form = $crawler->filter('form');
        $this->assertEquals(2,$form->count());
        $form = $form->first()->form();
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertEquals(200,$this->client->getResponse()->getStatusCode());
    }

    public function testDeleteRoom()
    {
        $this->logIn('Admin');
        $crawler = $this->client->request('GET', '/room/hors_service');
        $form = $crawler->filter('form')->first();
        $this->client->submit($form->form());
        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function setUp()
    {
        $this->client = static::createClient();
        $this->client->enableProfiler();
        $this->repository = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }
}