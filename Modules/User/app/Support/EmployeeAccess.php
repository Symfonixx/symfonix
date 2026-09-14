<?php

namespace Modules\User\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Modules\User\Models\Employee;

class EmployeeAccess
{
    public static function idForUser(?User $user): ?int
    {
        if (! $user) {
            return null;
        }

        return Employee::query()
            ->where(function (Builder $query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('email', $user->email);
            })
            ->value('id');
    }

    public static function assignableQuery(): Builder
    {
        return Employee::query()->assignable();
    }
}
