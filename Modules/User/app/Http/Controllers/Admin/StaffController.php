<?php

namespace Modules\User\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\User\app\Data\EmployeeData;
use Modules\User\app\Repositories\Employee\EmployeeRepository;
use Modules\User\Http\Requests\StoreEmployeeRequest;
use Modules\User\Http\Requests\UpdateEmployeeRequest;
use Modules\User\Models\Employee;

class StaffController extends Controller
{
    public function __construct(protected EmployeeRepository $employeeRepository)
    {
        $this->setActive('hr');
        $this->setActive('employees');
    }

    public function index()
    {
        $model = $this->employeeRepository->all();

        return view('user::.admin.staff.index', compact('model'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        $employeeData = EmployeeData::validateAndCreate($request->validated());
        $this->employeeRepository->store($employeeData);

        return redirect()->route('admin.employees.index');
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $employeeData = EmployeeData::validateAndCreate($request->validated());
        $this->employeeRepository->update($employeeData, $employee);

        return redirect()->route('admin.employees.index');
    }

    public function destroy(Employee $employee)
    {
        $this->employeeRepository->delete($employee);

        return response()->json([
            'success' => true,
        ]);
    }
}
