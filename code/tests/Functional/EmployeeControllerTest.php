<?php

namespace App\Tests\Functional;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EmployeeControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    public function testIndex()
    {
        $data = '{
            "Employee 1": "Employee 3",
            "Employee 2": "Employee 3",
            "Employee 3": "Employee 4",
            "Employee 4": "Employee 5",
            "Employee 6": "Employee 2",
            "Employee 7": "Employee 2"
        }';
        $this->client->request(
            'POST',
            '/employees',
            [],
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            $data
        );
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('{"message":"Created successfully"}', $this->client->getResponse()->getContent());

        $this->client->request(
            'GET',
            '/employees',
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        /**
        {
          "Employee 5": {
            "Employee 4": {
              "Employee 3": {
                "Employee 1": [],
                "Employee 2": {
                  "Employee 6": [],
                  "Employee 7": []
                }
              }
            }
          }
        }
         */
        $this->assertEquals(
            '{"Employee 5":{"Employee 4":{"Employee 3":{"Employee 1":[],"Employee 2":{"Employee 6":[],"Employee 7":[]}}}}}',
            $this->client->getResponse()->getContent()
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $container = static::getContainer();
        $entityManager = $container->get('doctrine')->getManager();
        $purger = new ORMPurger($entityManager);
        $purger->purge();
    }
}
