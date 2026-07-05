<?php

namespace Modules\User\app\Repositories\Leave;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\User\app\Data\LeaveData;
use Modules\User\Models\LeaveRequest;

interface LeaveRepository
{
    public function all(): LengthAwarePaginator;

    public function store(LeaveData $data): LeaveRequest;

    public function update(LeaveData $data, LeaveRequest $leaveRequest): LeaveRequest;

    public function delete(LeaveRequest $leaveRequest): bool;
}
