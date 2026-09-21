<?php

namespace App\Support\Notifications;

use App\Models\User;
use Illuminate\Support\Collection;

class NotificationRecipientResolver
{
    /**
     * @return Collection<int, User>
     */
    public function forPermission(string $permission, ?int $exceptUserId = null): Collection
    {
        return User::query()
            ->admins()
            ->permission($permission)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->when($exceptUserId, fn ($query) => $query->where('id', '!=', $exceptUserId))
            ->get();
    }
}
