<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\User\Database\Factories\CandidateFactory;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'expected_salary',
        'motivation',
        'resume_path',
        'cover_letter',
        'profile_notes',
    ];

    protected function casts(): array
    {
        return [
            'expected_salary' => 'decimal:2',
        ];
    }

    protected static function newFactory(): CandidateFactory
    {
        return CandidateFactory::new();
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }
}
