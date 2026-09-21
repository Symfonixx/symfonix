<?php

namespace Modules\Finance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Finance\Services\CurrencyService;
use Modules\User\Support\PermissionCatalog;

class UpdateDisplayCurrencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return PermissionCatalog::userMay($this->user(), $this->route()?->getName(), $this);
    }

    public function rules(): array
    {
        return [
            'currency' => [
                'required',
                'string',
                'size:3',
                Rule::in(app(CurrencyService::class)->supportedCurrencies()),
            ],
        ];
    }
}
