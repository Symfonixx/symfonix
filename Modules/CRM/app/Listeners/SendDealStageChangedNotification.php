<?php

namespace Modules\CRM\Listeners;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Modules\CRM\Events\DealStageChanged;
use Modules\CRM\Notifications\DealStageChangedNotification;
use Modules\User\Support\EmployeeAccess;

class SendDealStageChangedNotification
{
    public function handle(DealStageChanged $event): void
    {
        $event->deal->loadMissing(['company:id,name', 'assignee:id,email,name']);

        $recipients = collect();

        if ($event->deal->assignee && $event->deal->assignee->id !== EmployeeAccess::idForUser($event->changedBy)) {
            $assigneeUser = User::query()
                ->where('email', $event->deal->assignee->email)
                ->first();

            if ($assigneeUser) {
                $recipients->push($assigneeUser);
            }
        }

        $managers = User::permission('sales.deals.view_all')
            ->get()
            ->reject(fn (User $user) => $user->id === $event->changedBy?->id)
            ->reject(fn (User $user) => $recipients->contains('id', $user->id));

        $recipients = $recipients->merge($managers)->unique('id');

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send($recipients, new DealStageChangedNotification($event));
    }
}
