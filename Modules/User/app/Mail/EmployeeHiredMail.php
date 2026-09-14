<?php

namespace Modules\User\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Base\Models\Seo;
use Modules\User\Models\Employee;

class EmployeeHiredMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $mailLocale;

    public string $companyName;

    public function __construct(
        public Employee $employee,
        ?string $locale = null,
    ) {
        $this->mailLocale = $locale ?: app()->getLocale();
        $this->companyName = $this->resolveCompanyName();
        $this->locale($this->mailLocale);
    }

    public function build(): self
    {
        return $this->subject(trans('user::emails.hired.subject', [
            'company' => $this->companyName,
        ], $this->mailLocale))
            ->markdown('user::emails.employee-hired', [
                'employee' => $this->employee,
                'company' => $this->companyName,
                'position' => $this->employee->position,
            ]);
    }

    private function resolveCompanyName(): string
    {
        try {
            $name = Seo::get('website_name', config('app.name'));
            if (is_string($name) && $name !== '') {
                return $name;
            }
        } catch (\Throwable) {
            //
        }

        return (string) config('app.name');
    }
}
