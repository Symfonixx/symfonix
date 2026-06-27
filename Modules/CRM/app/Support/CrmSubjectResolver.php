<?php

namespace Modules\CRM\Support;

use Modules\CRM\Models\Lead;
use Illuminate\Database\Eloquent\Model;
use Modules\CRM\Models\Company;
use Modules\CRM\Models\Contact;
use Modules\CRM\Models\Deal;
use Modules\CRM\Models\Subscription;

class CrmSubjectResolver
{
    public const MAP = [
        'company' => Company::class,
        'contact' => Contact::class,
        'deal' => Deal::class,
        'subscription' => Subscription::class,
        'lead' => Lead::class,
    ];

    public static function resolve(string $type, int $id): Model
    {
        $class = self::MAP[$type] ?? null;

        if (! $class) {
            abort(422, __('crm::timeline.errors.invalid_subject'));
        }

        return $class::query()->findOrFail($id);
    }

    public static function typeFromModel(Model $model): string
    {
        return array_search($model::class, self::MAP, true) ?: class_basename($model);
    }
}
