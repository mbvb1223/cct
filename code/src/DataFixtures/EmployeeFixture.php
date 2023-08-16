<?php

namespace App\DataFixtures;

use App\Entity\Employee;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EmployeeFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $employee5 = new Employee();
        $employee5->setName('Employee 5');
        $manager->persist($employee5);
        $manager->flush();

        $employee4 = new Employee();
        $employee4->setName('Employee 4');
        $employee4->setParentId($employee5->getId());
        $manager->persist($employee4);
        $manager->flush();

        $employee3 = new Employee();
        $employee3->setName('Employee 3');
        $employee3->setParentId($employee4->getId());
        $manager->persist($employee3);
        $manager->flush();

        $employee2 = new Employee();
        $employee2->setName('Employee 2');
        $employee2->setParentId($employee3->getId());
        $manager->persist($employee2);
        $manager->flush();

        $employee1 = new Employee();
        $employee1->setName('Employee 1');
        $employee1->setParentId($employee3->getId());
        $manager->persist($employee1);
        $manager->flush();
    }
}
