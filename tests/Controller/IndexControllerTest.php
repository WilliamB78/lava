<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 09/04/2018
 * Time: 14:10
 */

namespace App\Tests\Controller;

use App\Tests\Traits\UserLogger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;

class IndexControllerTest extends WebTestCase
{

    use UserLogger;

    /** @var Client $client  */
    private $client = null;

    /**
     * Page de login
     */
    public function testHomePage()
    {

        $this->client->request('GET', '/');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Navbar des utilisateurs
     */
    public function testNavBarUser()
    {
        $this->logIn('User');
        $crawler = $this->client->request(
            'GET',
            '/index');

        $nav = $crawler->filter('nav')->count();
        $mesReservation = $crawler->filter('a:contains("Mes réservations")')->count();
        $this->assertEquals(1, $nav);
        $this->assertEquals(1, $mesReservation);
    }

    /**
     * Navbar des secrétaires
     */
    public function testNavBarSecretary()
    {
        $this->logIn('Secretaire');
        $crawler = $this->client->request(
            'GET',
            '/index');
        $nav = $crawler->filter('nav')->count();
        $demandes = $crawler->filter('a:contains("Demandes de réservation")')->count();
        $this->assertEquals(1, $nav);
        $this->assertEquals(1, $demandes);
    }

    /**
     * Navbar des admins
     */
    public function testNavBarAdmin()
    {
        $this->logIn('Admin');
        $crawler = $this->client->request(
            'GET',
            '/index');

        $nav = $crawler->filter('nav')->count();
        $users = $crawler->filter('a:contains("Utilisateur")')->count();
        $this->assertEquals(1,$nav);
        $this->assertEquals(1,$users);
    }


    public function setUp()
    {
        $this->client = static::createClient();
        $this->client->enableProfiler();
    }

    /**
     * @throws \Exception
     */
    public static function setUpBeforeClass()
    {
        $client = self::createClient();
        $application = new Application($client->getKernel());
        $application->setAutoExit(false);
        $application->run(new StringInput('doctrine:database:drop --force --env=test'));
        $application->run(new StringInput('doctrine:database:create --env=test'));
        $application->run(new StringInput('doctrine:migrations:migrate --no-interaction --env=test'));
        $application->run(new StringInput('doctrine:fixtures:load --env=test'));
    }
}