<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 17/04/2018
 * Time: 16:44.
 */

namespace App\Tests\Controller;

use App\Entity\Parametrage;
use App\Tests\Traits\UserLogger;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ParametrageControllerTest extends WebTestCase
{
    use UserLogger;
    /** @var Client $client */
    private $client = null;
    /** @var EntityManager $repository */
    private $repository;

    public function testIndex()
    {
        $this->logIn('Admin');
        $crawler = $this->client->request('GET', '/parametrage/index');
        $this->assertEquals('Parametrage', $crawler->filter('title')->text());
    }

    public function testChangeParametrage()
    {
        $this->logIn('Admin');

        $crawler = $this->client->request('GET', '/parametrage/index');

        $form = $crawler->filter('form')->first()->form();
        $form['value'] = 100;
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function setUp()
    {
        $this->client = static::createClient();
        $this->repository = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }
}
