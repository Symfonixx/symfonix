<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Models\Employee;

class CrmSalesTarget extends Model
{
    protected $fillable = [
        'employee_id',
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

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
