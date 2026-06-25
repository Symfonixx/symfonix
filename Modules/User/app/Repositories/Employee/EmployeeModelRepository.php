<?php

namespace Modules\User\app\Repositories\Employee;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Core\Traits\ExceptionHandlerTrait;
use Modules\Core\Traits\FileTrait;
use Modules\User\app\Data\EmployeeData;
use Modules\User\Models\Employee;

class EmployeeModelRepository implements EmployeeRepository
{
    use ExceptionHandlerTrait, FileTrait;

    public function all(): LengthAwarePaginator
    {
        return Employee::query()->latest()->paginate(config('core.page_size'));
    }

    public function find(int $id): Employee
    {
        return Employee::query()->findOrFail($id);
    }

    public function store(EmployeeData $data): Employee
    {
        return $this->execute(function () use ($data) {
            $employee = Employee::query()->create([
                'name' => $data->name,
                'email' => $data->email,
                'mobile' => $data->mobile,
                'status' => Employee::STATUS_ACTIVE,
            ]);

            session()->flushMessage(true);

            return $employee;
        });
    }

    public function update(EmployeeData $data, Employee $employee): Employee
    {
        return $this->execute(function () use ($data, $employee) {
            $employee->update([
                'name' => $data->name,
                'email' => $data->email,
                'mobile' => $data->mobile,
            ]);

            session()->flushMessage(true);

            return $employee;
        });
    }

    public function delete(Employee $employee): bool
    {
        return $this->execute(function () use ($employee) {
            if ($employee->img) {
                $this->deleteFile($employee->img);
            }

            $employee->delete();

            return true;
        });
    }

    public function count(): int
    {
        return Employee::query()->count();
    }
}
