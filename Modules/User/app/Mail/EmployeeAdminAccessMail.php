<?php

namespace Modules\User\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Base\Models\Seo;
use Modules\User\Models\Employee;

class EmployeeAdminAccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $mailLocale;

    public string $companyName;

    public string $loginUrl;

    public function __construct(
        public Employee $employee,
        public string $plainPassword,
        ?string $locale = null,
    ) {
        $this->mailLocale = $locale ?: app()->getLocale();
        $this->companyName = $this->resolveCompanyName();
        $this->loginUrl = (string) LaravelLocalization::getLocalizedURL($this->mailLocale, url('/login'));
        $this->locale($this->mailLocale);
    }

    public function build(): self
    {
        return $this->subject(trans('user::emails.admin.subject', [
            'company' => $this->companyName,
        ], $this->mailLocale))
            ->markdown('user::emails.employee-admin-access', [
                'employee' => $this->employee,
                'company' => $this->companyName,
                'email' => $this->employee->email,
                'password' => $this->plainPassword,
                'loginUrl' => $this->loginUrl,
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
