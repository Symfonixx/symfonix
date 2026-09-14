<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Modules\CRM\Models\Company;
use Modules\User\app\Models\AdminEventTrack;
use Modules\User\Models\Employee;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, TwoFactorAuthenticatable;

    public const TYPE_CUSTOMER = 'customer';

    public const TYPE_ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'last_login',
        'img',
        'type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $appends = ['avatar'];

    public function scopeType(Builder $builder, string $type = self::TYPE_CUSTOMER)
    {
        $builder->where('type', $type);
    }

    public function scopeCustomers(Builder $builder): void
    {
        $builder->where('type', self::TYPE_CUSTOMER);
    }

    public function scopeAdmins(Builder $builder): void
    {
        $builder->where('type', self::TYPE_ADMIN);
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function adminEventTracks(): HasMany
    {
        return $this->hasMany(AdminEventTrack::class)->latest('created_at');
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function isCustomer(): bool
    {
        return $this->type === self::TYPE_CUSTOMER;
    }

    public function isAdmin(): bool
    {
        return $this->type === self::TYPE_ADMIN;
    }

    /**
     * @return array<int, int>
     */
    public function companyIds(): array
    {
        return $this->companies()->pluck('id')->all();
    }

    public function getLastLoginHumanAttribute()
    {
        if ($this->last_login) {
            return $this->last_login->diffForHumans();
        }

        return null;
    }

    public function getAvatarAttribute()
    {
        $path = asset('images/avatar.png');

        if (! is_null($this->attributes['img'])) {
            $path = asset('storage/'.$this->attributes['img']);
        }

        return $path;
    }
}
