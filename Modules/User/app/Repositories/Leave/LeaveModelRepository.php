<?php

namespace Modules\User\app\Repositories\Leave;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Core\Traits\ExceptionHandlerTrait;
use Modules\User\app\Data\LeaveData;
use Modules\User\Models\LeaveRequest;

class LeaveModelRepository implements LeaveRepository
{
    use ExceptionHandlerTrait;

    public function all(): LengthAwarePaginator
    {
        return LeaveRequest::query()
            ->with('employee')
            ->latest()
            ->paginate(config('core.page_size'));
    }

    public function store(LeaveData $data): LeaveRequest
    {
        return $this->execute(function () use ($data) {
            $leaveRequest = LeaveRequest::query()->create($data->toArray());

            session()->flushMessage(true);

            return $leaveRequest;
        });
    }

    public function update(LeaveData $data, LeaveRequest $leaveRequest): LeaveRequest
    {
        return $this->execute(function () use ($data, $leaveRequest) {
            $leaveRequest->update($data->toArray());

            session()->flushMessage(true);

            return $leaveRequest;
        });
    }

    public function delete(LeaveRequest $leaveRequest): bool
    {
        return $this->execute(function () use ($leaveRequest) {
            $leaveRequest->delete();

            return true;
        });
    }
}
