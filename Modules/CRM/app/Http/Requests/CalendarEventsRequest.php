<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\User\Support\PermissionCatalog;

class CalendarEventsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return PermissionCatalog::userMay($this->user(), $this->route()?->getName(), $this);
    }

    public function rules(): array
    {
        return [
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after_or_equal:start'],
            'types' => ['sometimes', 'array'],
            'types.*' => ['string', 'in:activities,leads,deals,quotes,subscriptions,projects'],
        ];
    }
}
