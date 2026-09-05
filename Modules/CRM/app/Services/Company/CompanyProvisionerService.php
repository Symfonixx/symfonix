<?php

namespace Modules\CRM\Services\Company;

use App\Models\User;
use Illuminate\Support\Str;
use Modules\CRM\DTOs\Company\CompanyData;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Lead;

class CompanyProvisionerService
{
    public function __construct(private readonly CompanyService $companyService) {}

    public function provisionFromLead(Lead $lead): ?Company
    {
        if ($lead->company_id) {
            return $lead->company;
        }

        $companyName = trim((string) ($lead->company_name ?? ''));

        if ($companyName === '') {
            return null;
        }

        $user = $this->resolveCustomerUser($lead, $companyName);

        $existing = Company::query()
            ->where('user_id', $user->id)
            ->where('name', $companyName)
            ->first();

        if ($existing) {
            $lead->update(['company_id' => $existing->id]);

            return $existing;
        }

        $company = $this->companyService->create(CompanyData::fromRequest([
            'user_id' => $user->id,
            'name' => $companyName,
            'activity_type' => null,
            'email' => $lead->email,
            'phone' => $lead->phone,
            'country' => $lead->country,
            'city' => $lead->city,
            'address' => null,
            'notes' => null,
            'status' => Company::STATUS_ACTIVE,
        ]));

        if ($company) {
            $lead->update(['company_id' => $company->id]);
        }

        return $company;
    }

    private function resolveCustomerUser(Lead $lead, string $companyName): User
    {
        $mobile = $this->normalizeMobile($lead->phone);

        if ($lead->email) {
            $existing = User::query()
                ->where('email', $lead->email)
                ->customers()
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        if ($mobile) {
            $existing = User::query()
                ->where('mobile', $mobile)
                ->customers()
                ->first();

            if ($existing) {
                return $existing;
            }

            if (User::query()->where('mobile', $mobile)->exists()) {
                $mobile = null;
            }
        }

        $email = $lead->email;
        if (! $email || User::query()->where('email', $email)->exists()) {
            $email = $this->generatePlaceholderEmail($lead);
        }

        return User::create([
            'name' => $lead->name ?: $companyName,
            'email' => $email,
            'mobile' => $mobile,
            'password' => bcrypt(Str::random(32)),
            'type' => User::TYPE_CUSTOMER,
        ]);
    }

    private function normalizeMobile(?string $phone): ?string
    {
        $mobile = preg_replace('/\D/', '', (string) $phone);

        return $mobile !== '' ? $mobile : null;
    }

    private function generatePlaceholderEmail(Lead $lead): string
    {
        return 'lead-'.$lead->id.'-'.Str::lower(Str::random(8)).'@symfonix.local';
    }
}
