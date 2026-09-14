<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'img' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1048',
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'mobile' => [
                'required',
                'string',
                'regex:/^[0-9]{10,15}$/',
                'unique:users,mobile',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (str_starts_with((string) $value, '09') && strlen((string) $value) !== 11) {
                        $fail(__('Syrian mobile numbers must be exactly 11 digits.'));
                    }
                },
            ],
            'password' => 'required|min:6',
        ];
    }

    public function authorize(): bool
    {
        return $this->user()?->can('sales.customers.create') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('mobile')) {
            $this->merge([
                'mobile' => preg_replace('/\D/', '', (string) $this->input('mobile')),
            ]);
        }
    }
}
