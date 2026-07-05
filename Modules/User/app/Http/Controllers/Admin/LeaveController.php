<?php

namespace Modules\User\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\User\app\Data\LeaveData;
use Modules\User\app\Repositories\Leave\LeaveRepository;
use Modules\User\Http\Requests\StoreLeaveRequestRequest;
use Modules\User\Http\Requests\UpdateLeaveRequestRequest;
use Modules\User\Models\Employee;
use Modules\User\Models\LeaveRequest;

class LeaveController extends Controller
{
    public function __construct(protected LeaveRepository $leaveRepository)
    {
        $this->setActive('hr');
        $this->setActive('leaves');
    }

    public function index()
    {
        $model = $this->leaveRepository->all();
        $employees = Employee::query()->orderBy('name')->get();
        $types = LeaveRequest::types();
        $statuses = LeaveRequest::statuses();

        return view('user::admin.leave.index', compact('model', 'employees', 'types', 'statuses'));
    }

    public function store(StoreLeaveRequestRequest $request)
    {
        $leaveData = LeaveData::validateAndCreate($request->validated());
        $this->leaveRepository->store($leaveData);

        return redirect()->route('admin.leaves.index');
    }

    public function update(UpdateLeaveRequestRequest $request, LeaveRequest $leaveRequest)
    {
        $leaveData = LeaveData::validateAndCreate($request->validated());
        $this->leaveRepository->update($leaveData, $leaveRequest);

        return redirect()->route('admin.leaves.index');
    }

    public function destroy(LeaveRequest $leaveRequest)
    {
        $this->leaveRepository->delete($leaveRequest);

        return response()->json([
            'success' => true,
        ]);
    }
}
