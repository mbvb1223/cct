<?php

namespace App\Tests\Integration;

use App\DataFixtures\EmployeeFixture;
use App\Service\EmployeeService;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EmployeeTest extends KernelTestCase
{
    private EmployeeService $service;

    /**
     * @dataProvider employeesProvider
     */
    public function testGetTreeByNameAndLevel($name, $parentLevel = null, $childLevel = null, $expected = [])
    {
        $employee = $this->service->findOneBy(['name' => $name]);
        $actual = $this->service->getTreeByNameAndLevel($employee, $parentLevel, $childLevel);

        $this->assertEquals($expected, $actual);
    }

    public function employeesProvider(): \Generator
    {
        yield ['Employee 2', 1, 1, ["Employee 3" => ["Employee 2" => []]]];
        yield ['Employee 2', null, null, [
            "Employee 5" => [
                "Employee 4" => [
                    "Employee 3" => [
                        "Employee 2" => [
                        ]
                    ]
                ]
            ]
        ]];
        yield ['Employee 4', 1, 1, [
            "Employee 5" => [
                "Employee 4" => [
                    "Employee 3" => []
                ]
            ]
        ]];
        yield ['Employee 3', 1, null, [
            "Employee 4" => [
                "Employee 3" => [
                    "Employee 1" => [],
                    "Employee 2" => []
                ]
            ]
        ]];
        yield ['Employee 4', null, 1, [
            "Employee 5" => [
                "Employee 4" => [
                    "Employee 3" => []
                ]
            ]
        ]];
    }

    public function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->service = $container->get(EmployeeService::class);

        $entityManager = $container->get('doctrine')->getManager();
        $loader = new Loader();
        $loader->addFixture(new EmployeeFixture());
        $executor = new ORMExecutor($entityManager, new ORMPurger());
        $executor->execute($loader->getFixtures());
    }
}