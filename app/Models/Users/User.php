<?php

declare(strict_types=1);

namespace App\Models\Users;

use App\Models\Advertisements\Advertisement;
use App\Models\Payments\Payment;
use App\Models\Roles\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    public const ID = 'id';
    public const NAME = 'name';
    public const EMAIL = 'email';
    public const PASSWORD = 'password';
    public const PHONE = 'phone';
    public const ROLE_ID = 'role_id';
    public const IS_ACTIVE = 'is_active';
    public const EMAIL_VERIFIED_AT = 'email_verified_at';
    public const PHONE_VERIFIED_AT = 'phone_verified_at';
    public const REMEMBER_TOKEN = 'remember_token';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $fillable = [
        self::NAME,
        self::EMAIL,
        self::PASSWORD,
        self::PHONE,
        self::ROLE_ID,
        self::IS_ACTIVE,
        self::EMAIL_VERIFIED_AT,
        self::PHONE_VERIFIED_AT,
    ];

    protected $hidden = [
        self::PASSWORD,
        self::REMEMBER_TOKEN,
    ];

    protected function casts(): array
    {
        return [
            self::EMAIL_VERIFIED_AT => 'datetime',
            self::PHONE_VERIFIED_AT => 'datetime',
            self::PASSWORD => 'hashed',
            self::IS_ACTIVE => 'boolean',
        ];
    }

    public function roleRelation(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function advertisementsRelation(): HasMany
    {
        return $this->hasMany(Advertisement::class);
    }

    public function paymentsRelation(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->role && $this->role->name === $role;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function getId(): int
    {
        return $this->getAttribute(self::ID);
    }
}
