<?php

namespace Modules\CRM\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmDashboardLayout extends Model
{
    protected $fillable = [
        'user_id',
        'widgets',
    ];

    protected function casts(): array
    {
        return [
            'widgets' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
