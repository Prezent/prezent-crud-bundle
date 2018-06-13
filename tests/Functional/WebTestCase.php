<?php

namespace Prezent\CrudBundle\Tests\Functional;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\BrowserKit\Client;

/**
 * @author Sander Marechal
 */
abstract class WebTestCase extends BaseWebTestCase
{
    protected static function createKernel(array $options = [])
    {
        $class = self::getKernelClass();

        if (!isset($options['test_case'])) {
            throw new \InvalidArgumentException('The option "test_case" must be set.');
        }

        return new $class($options['test_case'], 'test', true);
    }

    protected static function createClient(array $options = [], array $server = [])
    {
        $options['test_case'] = substr(strrchr(static::class, '\\'), 1);

        return parent::createClient($options, $server);
    }

    protected static function createDatabase(Client $client)
    {
        $om = $client->getContainer()->get('doctrine.orm.default_entity_manager');
        $metadata = $om->getMetadataFactory()->getAllMetadata();

        $schemaTool = new SchemaTool($om);
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    protected static function loadFixtures(Client $client, $fixtures)
    {
        if (!is_array($fixtures)) {
            $fixtures = [$fixtures];
        }

        $om = $client->getContainer()->get('doctrine.orm.default_entity_manager');
        $executor = new ORMExecutor($om, new ORMPurger($om));
        $executor->execute($fixtures);
    }
}
