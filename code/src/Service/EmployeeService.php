<?php

namespace App\Service;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;

class EmployeeService
{
    private EmployeeRepository $employeeRepository;

    public function __construct(
        EmployeeRepository $productRepository
    ) {
        $this->employeeRepository = $productRepository;
    }

    public function mapEmployeeTree(array $groupByParentId, int $parentId = 0): array
    {
        $data = [];
        if (!isset($groupByParentId[$parentId])) {
            return [];
        }

        foreach ($groupByParentId[$parentId] as $employee) {
            $data[$employee->getName()] = $this->mapEmployeeTree($groupByParentId, $employee->getId());
        }

        return $data;
    }

    public function save(Employee $employee): Employee
    {
        return $this->employeeRepository->save($employee);
    }

    public function find($id): ?Employee
    {
        return $this->employeeRepository->find($id);
    }

    public function findAll(): array
    {
        return $this->employeeRepository->findAll();
    }

    public function findOneBy(array $criteria, array $orderBy = null): ?Employee
    {
        return $this->employeeRepository->findOneBy($criteria, $orderBy);
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        return $this->employeeRepository->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function whereIn(string $field, array $values)
    {
        return $this->employeeRepository->whereIn($field, $values);
    }
}
