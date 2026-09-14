<?php

namespace Modules\User\Services;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\User\Mail\EmployeeAdminAccessMail;
use Modules\User\Mail\EmployeeHiredMail;
use Modules\User\Models\Employee;

class EmployeeNotificationService
{
    public function sendHired(Employee $employee): void
    {
        $this->send(new EmployeeHiredMail($employee, app()->getLocale()), $employee->email);
    }

    public function sendAdminAccess(Employee $employee, string $plainPassword): void
    {
        $this->send(
            new EmployeeAdminAccessMail($employee, $plainPassword, app()->getLocale()),
            $employee->email,
        );
    }

    private function send(Mailable $mailable, ?string $email): void
    {
        if (! filled($email)) {
            return;
        }

        try {
            Mail::to($email)->send($mailable);
        } catch (\Throwable $e) {
            Log::error('Failed to send employee notification email.', [
                'email' => $email,
                'mailable' => $mailable::class,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
