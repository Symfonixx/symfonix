<?php

namespace Modules\CRM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmSalesTarget extends Model
{
    protected $fillable = [
        'user_id',
        'deals_target',
        'value_target',
    ];

    protected function casts(): array
    {
        return [
            'deals_target' => 'integer',
            'value_target' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
