<?php

namespace Modules\User\app\Repositories\Employee;

use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Traits\ExceptionHandlerTrait;
use Modules\Core\Traits\FileTrait;
use Modules\User\app\Data\EmployeeData;
use Modules\User\Models\Employee;

class EmployeeModelRepository implements EmployeeRepository
{
    use ExceptionHandlerTrait, FileTrait;

    public function all(): LengthAwarePaginator
    {
        return Employee::query()->with(['user', 'team'])->latest()->paginate(config('core.page_size'));
    }

    public function find(int $id): Employee
    {
        return Employee::query()->findOrFail($id);
    }

    public function store(EmployeeData $data, ?UploadedFile $resume = null): Employee
    {
        return $this->execute(function () use ($data, $resume) {
            $employee = Employee::query()->create([
                'name' => $data->name,
                'email' => $data->email,
                'mobile' => $data->mobile,
                'position' => $data->position,
                'resume' => $this->storeResume($resume, $data->resume),
                'status' => Employee::STATUS_ACTIVE,
            ]);

            session()->flushMessage(true);

            return $employee;
        });
    }

    public function update(EmployeeData $data, Employee $employee, ?UploadedFile $resume = null): Employee
    {
        return $this->execute(function () use ($data, $employee, $resume) {
            $employee->update([
                'name' => $data->name,
                'email' => $data->email,
                'mobile' => $data->mobile,
                'position' => $data->position,
                'resume' => $this->storeResume($resume, $employee->resume),
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

            if ($employee->resume) {
                Storage::disk('public')->delete($employee->resume);
            }

            $employee->delete();

            return true;
        });
    }

    public function count(): int
    {
        return Employee::query()->count();
    }

    public function storeResume(?UploadedFile $file, ?string $existing = null): ?string
    {
        if (! $file) {
            return $existing;
        }

        if ($existing) {
            Storage::disk('public')->delete($existing);
        }

        return $file->store('employees/resumes', 'public');
    }
}
