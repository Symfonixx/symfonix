<?php

namespace Modules\User\app\Repositories\Employee;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\User\app\Data\EmployeeData;
use Modules\User\Models\Employee;

interface EmployeeRepository
{
    public function all(): LengthAwarePaginator;

    public function find(int $id): Employee;

    public function store(EmployeeData $data): Employee;

    public function update(EmployeeData $data, Employee $employee): Employee;

    public function delete(Employee $employee): bool;

    public function count(): int;
}
