<?php
namespace App\Tests\Unit;

use App\Entity\Employee;

class TestableEmployee extends Employee
{
    private int $id;

    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}