<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactForm extends Model
{
    protected $fillable = [
        'company_id',
        'service_id',
        'lead_id',
        'contact_id',
        'converted_at',
        'ip_address',
        'name',
        'email',
        'mobile',
        'subject',
        'message',
        'blocked',
    ];

    protected $casts = [
        'blocked' => 'boolean',
        'converted_at' => 'datetime',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(\Modules\Services\Models\Service::class);
    }

    public function services(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(\Modules\Services\Models\Service::class, 'contact_form_service')
            ->withTimestamps();
    }

    /** Service ids from the pivot, falling back to the legacy single column. */
    public function serviceIds(): array
    {
        $ids = $this->services->pluck('id')->map(fn ($id) => (int) $id)->all();

        if ($ids === [] && $this->service_id) {
            $ids = [(int) $this->service_id];
        }

        return $ids;
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function crmContact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
}
