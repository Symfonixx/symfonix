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
