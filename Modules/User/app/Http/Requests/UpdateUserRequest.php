<?php

namespace Modules\User\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\User\Support\PermissionCatalog;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return PermissionCatalog::userMay($this->user(), $this->route()?->getName(), $this);
    }

    public function rules(): array
    {
        $userId = $this->routeUserId();

        return [
            'img' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1048',
            'name' => 'required|min:3',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'mobile' => [
                'required',
                'string',
                'regex:/^[0-9]{10,15}$/',
                Rule::unique('users', 'mobile')->ignore($userId),
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (str_starts_with((string) $value, '09') && strlen((string) $value) !== 11) {
                        $fail(__('Syrian mobile numbers must be exactly 11 digits.'));
                    }
                },
            ],
            'password' => 'nullable|min:6',
        ];
    }

    protected function prepareForValidation(): void
    {
        $payload = [];

        if ($this->has('mobile')) {
            $payload['mobile'] = preg_replace('/\D/', '', (string) $this->input('mobile'));
        }

        if ($this->input('password') === '') {
            $payload['password'] = null;
        }

        if ($payload !== []) {
            $this->merge($payload);
        }
    }

    private function routeUserId(): mixed
    {
        $param = $this->route('admin') ?? $this->route('customer');

        return $param instanceof User ? $param->id : $param;
    }
}
