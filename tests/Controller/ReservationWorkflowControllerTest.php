<?php
/**
 * Created by PhpStorm.
 * User: coubardalexis
 * Date: 11/04/2018
 * Time: 11:07
 */

namespace App\Tests\Controller;

use App\Tests\Traits\UserLogger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Console\Input\StringInput;

class ReservationWorkflowControllerTest extends WebTestCase
{
    use UserLogger;
    /** @var Client $client  */
    private $client = null;

    public function testReservationSecretaryApprove()
    {
        $this->logIn('Secretaire');
        $this->client->request('GET', '/reservations/secretary/approve/demand_validation/1');
        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testReservationUserApprove()
    {
        $this->logIn('User');
        $this->client->request('GET', '/reservations/user/approve/cancel_demand/2');
        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function setUp()
    {
        $this->client = static::createClient();
        $this->client->enableProfiler();
    }
}