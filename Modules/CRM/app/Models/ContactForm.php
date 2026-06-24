<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactForm extends Model
{
    protected $fillable = [
        'company_id',
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
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
