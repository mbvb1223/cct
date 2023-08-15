<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Service\EmployeeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EmployeeController extends AbstractController
{
    private EmployeeService $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    #[Route('/employees', name: 'employee_import', methods: 'post')]
    public function import(Request $request): JsonResponse
    {
        $data = $request->toArray();
        $employeeNames = array_unique(array_merge(array_keys($data), array_values($data)));
        $existingEmployees = $this->getExistingEmployeesByName($employeeNames);
        foreach ($data as $employeeName => $supervisorName) {
            $supervisor = $this->createSupervisor($existingEmployees, $supervisorName);

            $this->createEmployee($existingEmployees, $employeeName, $supervisor->getId());
        }

        return $this->json([
            'message' => 'Created successfully',
        ]);
    }

    #[Route('/employees', name: 'employee_index', methods: 'get')]
    public function index(): JsonResponse
    {
        $employees = $this->employeeService->findAll();
        $groupByParentId = [];
        array_walk($employees, function ($employee) use (&$groupByParentId) {
            return $groupByParentId[$employee->getParentId() ?? 0][] = $employee;
        });

        $result = $this->employeeService->mapEmployeeTree($groupByParentId);

        return $this->json($result);
    }

    #[Route('/employees/{name}', name: 'employee_show', methods: 'get')]
    public function get(Request $request): JsonResponse
    {
        $employee = $this->employeeService->findOneBy(['name' => $request->get('name')]);
        $parentLevel = $request->get('level');
        $childLevel = $request->get('level');
        $result = $this->employeeService->getTreeByNameAndLevel(
            $employee,
            $parentLevel,
            $childLevel
        );

        return $this->json($result);
    }

    private function getExistingEmployeesByName(array $employeeNames): array
    {
        $existingEmployees = $this->employeeService->whereIn('name', $employeeNames);
        $employeesKeyByName = [];
        foreach ($existingEmployees as $employee) {
            $employeesKeyByName[$employee->getName()] = $employee;
        }

        return $employeesKeyByName;
    }

    private function createSupervisor(array &$existingEmployees, string $name): Employee
    {
        $supervisor = $existingEmployees[$name] ?? null;
        if (!$supervisor) {
            $employeeObject = new Employee();
            $employeeObject->setName($name);
            $supervisor = $this->employeeService->save($employeeObject);
            $existingEmployees[$name] = $supervisor;
        }

        return $supervisor;
    }

    private function createEmployee(array &$existingEmployees, string $name, ?int $parentId): Employee
    {
        $employee = $existingEmployees[$name] ?? null;
        if (!$employee) {
            $employee = new Employee();
            $employee->setName($name);
            $existingEmployees[$name] = $employee;
        }
        $employee->setParentId($parentId);
        $this->employeeService->save($employee);

        return $employee;
    }
}
