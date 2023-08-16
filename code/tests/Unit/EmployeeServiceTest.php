<?php
namespace App\Tests\Unit;

use App\Repository\EmployeeRepository;
use App\Service\EmployeeService;
use PHPUnit\Framework\TestCase;

class EmployeeServiceTest extends TestCase
{
    public function testGroupByParentId(): void
    {
        $repo = $this->createMock(EmployeeRepository::class);
        $service = new EmployeeService($repo);

        $employees = $this->provideEmployees();

        $actual = $service->groupByParentId($employees);

        $this->assertCount(2, $actual[3]);
        $this->assertSame('Employee 1', $actual[3][0]->getName());
        $this->assertSame('Employee 2', $actual[3][1]->getName());
        $this->assertSame(3, $actual[3][1]->getParentId());

        $this->assertCount(1, $actual[4]);
        $this->assertSame('Employee 3', $actual[4][0]->getName());
        $this->assertSame(4, $actual[4][0]->getParentId());

        $this->assertCount(1, $actual[5]);
        $this->assertSame('Employee 4', $actual[5][0]->getName());

        $this->assertCount(1, $actual[0]);
        $this->assertSame('Employee 5', $actual[0][0]->getName());
    }

    public function testMapEmployeeTree()
    {
        $repo = $this->createMock(EmployeeRepository::class);
        $service = new EmployeeService($repo);

        $employees = $this->provideEmployees();
        $groupByParentId = $service->groupByParentId($employees);
        $actual = $service->mapEmployeeTree($groupByParentId);

        $this->assertEquals([
            "Employee 5" => [
                "Employee 4" => [
                    "Employee 3" => [
                        "Employee 1" => [],
                        "Employee 2" => []
                    ],
                ],
            ],
        ], $actual);
    }

    /**
     * @return TestableEmployee[]
     */
    private function provideEmployees(): array
    {
        $employee1 = new TestableEmployee();
        $employee1->setId(1);
        $employee1->setName('Employee 1');
        $employee1->setParentId(3);

        $employee2 = new TestableEmployee();
        $employee2->setId(2);
        $employee2->setName('Employee 2');
        $employee2->setParentId(3);

        $employee3 = new TestableEmployee();
        $employee3->setId(3);
        $employee3->setName('Employee 3');
        $employee3->setParentId(4);

        $employee4 = new TestableEmployee();
        $employee4->setId(4);
        $employee4->setName('Employee 4');
        $employee4->setParentId(5);

        $employee5 = new TestableEmployee();
        $employee5->setId(5);
        $employee5->setName('Employee 5');
        $employee5->setParentId(null);

        return [$employee1, $employee2, $employee3, $employee4, $employee5];
    }
}