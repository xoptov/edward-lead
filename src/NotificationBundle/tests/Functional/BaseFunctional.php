<?php

namespace NotificationBundle\tests\Functional;

use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseFunctional extends WebTestCase
{
    /**
     * @var  KernelInterface
     */
    public $client;

    /**
     * @var  EntityManager
     */
    public $em;

    public function setUp()
    {
        $this->client = self::createClient();
        $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->generateSchema();
    }

    /**
     * @return null
     */
    protected function generateSchema()
    {
        $metadatas = $this->getMetadatas();
        if (!empty($metadatas)) {
            $tool = new SchemaTool($this->em);
            $tool->dropSchema($metadatas);
            $tool->createSchema($metadatas);
        }
    }

    /**
     * @return array
     */
    protected function getMetadatas()
    {
        return $this->em->getMetadataFactory()->getAllMetadata();
    }

}