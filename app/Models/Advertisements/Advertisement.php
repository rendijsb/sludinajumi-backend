<?php

namespace App\Models\Advertisements;

use App\Models\Categories\Category;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Advertisement extends Model
{
    public const ID = 'id';
    public const TITLE = 'title';
    public const DESCRIPTION = 'description';
    public const PRICE = 'price';
    public const CURRENCY = 'currency';
    public const STATUS = 'status';
    public const USER_ID = 'user_id';
    public const CATEGORY_ID = 'category_id';
    public const LOCATION = 'location';
    public const CONTACT_PHONE = 'contact_phone';
    public const CONTACT_EMAIL = 'contact_email';
    public const IMAGES = 'images';
    public const EXPIRES_AT = 'expires_at';
    public const FEATURED_UNTIL = 'featured_until';
    public const VIEWS_COUNT = 'views_count';
    public const IS_NEGOTIABLE = 'is_negotiable';

    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        self::TITLE,
        self::DESCRIPTION,
        self::PRICE,
        self::CURRENCY,
        self::STATUS,
        self::USER_ID,
        self::CATEGORY_ID,
        self::LOCATION,
        self::CONTACT_PHONE,
        self::CONTACT_EMAIL,
        self::IMAGES,
        self::EXPIRES_AT,
        self::FEATURED_UNTIL,
        self::VIEWS_COUNT,
        self::IS_NEGOTIABLE,
    ];

    protected function casts(): array
    {
        return [
            self::IMAGES => 'array',
            self::EXPIRES_AT => 'datetime',
            self::FEATURED_UNTIL => 'datetime',
            self::IS_NEGOTIABLE => 'boolean',
            self::PRICE => 'decimal:2',
        ];
    }

    public function userRelation(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categoryRelation(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function paymentsRelation(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeActive($query)
    {
        return $query->where(self::STATUS, self::STATUS_ACTIVE)
            ->where(self::EXPIRES_AT, '>', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where(self::FEATURED_UNTIL, '>', now());
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE && $this->expires_at > now();
    }

    public function isFeatured(): bool
    {
        return $this->featured_until && $this->featured_until > now();
    }

    public function incrementViews(): void
    {
        $this->increment(self::VIEWS_COUNT);
    }
}
