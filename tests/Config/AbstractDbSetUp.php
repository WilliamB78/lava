<?php
/**
 * Created by PhpStorm.
 * User: bmnk
 * Date: 09/04/18
 * Time: 18:55.
 */

namespace App\Tests\Config;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class AbstractDbSetUp extends KernelTestCase
{
    public static function prime()
    {
        self::bootKernel();
        // Make sure we are in the test environment
        if ('test' !== self::$kernel->getEnvironment()) {
            throw new \LogicException('Primer must be executed in the test environment');
        }

        // Get the entity manager from the service container
        $entityManager = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');

        // Run the schema update tool using our entity metadata
        $metadatas = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->updateSchema($metadatas);

        // Get the entity manager from the service container
        $entityManager = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        // Ifclear you are using the Doctrine Fixtures Bundle you could load these here
        $loader = new Loader();

        $loader->loadFromDirectory(__DIR__.'/testFixtures/');

        $purger = new ORMPurger();
        $executor = new ORMExecutor($entityManager, $purger);
        $executor->execute($loader->getFixtures());
    }

    /**
     * Returns the doctrine orm entity manager.
     *
     * @return object
     */
    public static function getEntityManager()
    {
        return self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }

    public static function getSession()
    {
        return self::$kernel->getContainer()->get('session');
    }

    public static function getEventDispatcher()
    {
        return self::$kernel->getContainer()->get('event_dispatcher');
    }

    public static function getFormFactory()
    {
        return self::$kernel->getContainer()->get('form.factory');
    }
}
