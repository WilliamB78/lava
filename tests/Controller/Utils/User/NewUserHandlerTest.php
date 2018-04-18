<?php
/**
 * Created by PhpStorm.
 * User: hello
 * Date: 12/04/2018
 * Time: 15:38.
 */

namespace App\Tests\Controller\Utils\User;

use App\Entity\User;
use App\Form\UserType;
use App\Service\UserMail;
use App\Tests\Config\AbstractDbSetUp;
use App\Tests\Traits\UserLogger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormFactoryInterface;

class NewUserHandlerTest extends WebTestCase
{
    use UserLogger;
    /** @var Client $client */
    private $client;
    /** @var EntityManagerInterface $em */
    private $em;

    /** @var FormFactoryInterface $formFactory */
    private $formFactory;

    /** @var UserMail $userMailer */
    private $userMailer;

    /** @var EventDispatcherInterface $dispatcher */
    private $dispatcher;

    public function setUp()
    {
        AbstractDbSetUp::prime();
        $this->dispatcher = AbstractDbSetUp::getEventDispatcher();
        $this->formFactory = AbstractDbSetUp::getFormFactory();
        $this->em = AbstractDbSetUp::getEntityManager();
        $this->client = static::createClient();
    }

    public function testCreateForm()
    {
        $user = new User();
        $newForm = $this->formFactory->create(UserType::class, $user);
        $newForm->add('roles', ChoiceType::class, array(
            'choices' => array(
                'USER' => 'ROLE_USER',
                'SECRETARY' => 'ROLE_SECRETARY',
                'ADMIN' => 'ROLE_ADMIN',
            ),
        ));
        $this->assertTrue($newForm->isSynchronized());
    }
}
