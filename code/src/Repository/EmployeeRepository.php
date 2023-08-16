<?php

namespace App\Repository;

use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Employee>
 *
 * @method Employee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employee[]    findAll()
 * @method Employee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    public function save(Employee $employee): Employee
    {
        $this->_em->persist($employee);
        $this->_em->flush();

        return $employee;
    }

    public function whereIn(string $field, array $values)
    {
        return $this->createQueryBuilder('p')
            ->andWhere("p.$field IN(:values)")
            ->setParameter('values', $values)
            ->getQuery()
            ->getResult();
    }
}
