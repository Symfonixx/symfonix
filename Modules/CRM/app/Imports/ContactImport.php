<?php

namespace Modules\CRM\Imports;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Modules\CRM\DTOs\Contact\ContactData;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Contact;
use Modules\CRM\Services\Contact\ContactService;

class ContactImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    public function __construct(private readonly ContactService $contactService) {}

    public function model(array $row)
    {
        $name = trim((string) ($row['name'] ?? ''));

        if ($name === '') {
            return null;
        }

        $email = $this->nullableTrim($row['email'] ?? null);
        $phone = $this->nullableTrim($row['phone'] ?? null);
        $phone2 = $this->nullableTrim($row['phone2'] ?? null);
        $source = $this->resolveSource($row['source'] ?? null);
        $jobTitle = $this->nullableTrim($row['job_title'] ?? null);
        $notes = $this->nullableTrim($row['notes'] ?? null);
        $isPrimary = $this->parseBoolean($row['is_primary'] ?? null);
        $companyId = $this->resolveCompanyId($row['company'] ?? null);
        $userId = $this->resolveCustomerId($row['customer_email'] ?? null);

        $payload = [
            'company_id' => $companyId,
            'user_id' => $userId,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'phone2' => $phone2,
            'source' => $source,
            'job_title' => $jobTitle,
            'notes' => $notes,
            'is_primary' => $isPrimary,
        ];

        $existing = $this->findExisting($email, $phone);

        if ($existing) {
            $this->contactService->update($existing, ContactData::fromRequest($payload));

            return null;
        }

        $this->contactService->create(ContactData::fromRequest($payload));

        return null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50', 'regex:/^[0-9+\-\s()]+$/'],
            'phone2' => ['nullable', 'string', 'max:50', 'regex:/^[0-9+\-\s()]+$/'],
            'source' => ['nullable', Rule::in(Contact::SOURCES)],
            'job_title' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'is_primary' => ['nullable'],
            'company' => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function prepareForValidation(array $data, int $index): array
    {
        foreach (['name', 'email', 'phone', 'phone2', 'source', 'job_title', 'notes', 'company', 'customer_email', 'is_primary'] as $field) {
            if (array_key_exists($field, $data) && $data[$field] !== null && ! is_string($data[$field])) {
                $data[$field] = is_bool($data[$field])
                    ? ($data[$field] ? '1' : '0')
                    : (string) $data[$field];
            }
        }

        return $data;
    }

    private function findExisting(?string $email, ?string $phone): ?Contact
    {
        if ($email !== null) {
            $byEmail = Contact::query()->where('email', $email)->first();

            if ($byEmail) {
                return $byEmail;
            }
        }

        if ($phone !== null) {
            return Contact::query()->where('phone', $phone)->first();
        }

        return null;
    }

    private function resolveCompanyId(mixed $company): ?int
    {
        $value = $this->nullableTrim($company);

        if ($value === null) {
            return null;
        }

        if (ctype_digit($value)) {
            $byId = Company::query()->find((int) $value);

            return $byId?->id;
        }

        return Company::query()
            ->whereRaw('LOWER(name) = ?', [Str::lower($value)])
            ->value('id');
    }

    private function resolveCustomerId(mixed $customerEmail): ?int
    {
        $email = $this->nullableTrim($customerEmail);

        if ($email === null) {
            return null;
        }

        return User::query()
            ->customers()
            ->where('email', $email)
            ->value('id');
    }

    private function resolveSource(mixed $source): ?string
    {
        $value = $this->nullableTrim($source);

        if ($value === null) {
            return 'manual';
        }

        $normalized = Str::snake(Str::lower($value));

        if (in_array($normalized, Contact::SOURCES, true)) {
            return $normalized;
        }

        return in_array($value, Contact::SOURCES, true) ? $value : 'manual';
    }

    private function parseBoolean(mixed $value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        return in_array(Str::lower(trim((string) $value)), ['yes', '1', 'true', 'y'], true);
    }

    private function nullableTrim(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }
}
