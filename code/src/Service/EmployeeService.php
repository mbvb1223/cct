<?php

namespace App\Service;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;

class EmployeeService
{
    private EmployeeRepository $employeeRepository;

    public function __construct(
        EmployeeRepository $employeeRepository
    ) {
        $this->employeeRepository = $employeeRepository;
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

    public function getTreeByNameAndLevel(Employee $employee, int $parentLevel = null, int $childLevel = null): array
    {
        $employees[] = $employee;
        $this->getParentLevel($employees, $employee, $parentLevel);
        $this->getChildLevel($employees, [$employee], $childLevel);

        $groupByParentId = [];
        array_walk($employees, function ($employee) use (&$groupByParentId) {
            return $groupByParentId[$employee->getParentId() ?? 0][] = $employee;
        });

        return $this->mapEmployeeTree($groupByParentId);
    }

    private function getParentLevel(&$result, Employee $employee, int $level = null)
    {
        if (!$employee->getParentId() || $level === 0) {
            return;
        }
        $parentEmployee = $this->findOneBy(['id' => $employee->getParentId()]);
        $result[] = $parentEmployee;

        $level = $level ? $level - 1 : null;
        $this->getParentLevel($result, $parentEmployee, $level);
    }

    private function getChildLevel(&$result, array $employees, int $level = null)
    {
        $ids = [];
        array_walk($employees, function($employee) use (&$ids) {
            $ids[] = $employee->getId();
        });
        $childEmployees = $this->whereIn('parent_id', $ids);

        if (!$childEmployees || $level === 0) {
            return [];
        }
        $result = array_merge($result, $childEmployees);

        $level = $level ? $level - 1 : null;
        $this->getChildLevel($result, $childEmployees, $level);
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
