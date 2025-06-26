<?php

namespace App\Models\Categories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    public const ID = 'id';
    public const NAME = 'name';
    public const SLUG = 'slug';
    public const DESCRIPTION = 'description';
    public const PARENT_ID = 'parent_id';
    public const IS_ACTIVE = 'is_active';
    public const SORT_ORDER = 'sort_order';
    public const ICON = 'icon';

    protected $fillable = [
        self::NAME,
        self::SLUG,
        self::DESCRIPTION,
        self::PARENT_ID,
        self::IS_ACTIVE,
        self::SORT_ORDER,
        self::ICON,
    ];

    protected function casts(): array
    {
        return [
            self::IS_ACTIVE => 'boolean',
        ];
    }

    public function parentRelation(): BelongsTo
    {
        return $this->belongsTo(Category::class, self::PARENT_ID, Category::ID);
    }

    public function childrenRelation(): HasMany
    {
        return $this->hasMany(Category::class, self::ID, Category::PARENT_ID);
    }

    public function advertisementsRelation(): HasMany
    {
        return $this->hasMany(Advertisement::class);
    }

    public function scopeActive($query)
    {
        return $query->where(self::IS_ACTIVE, true);
    }

    public function scopeParent($query)
    {
        return $query->whereNull(self::PARENT_ID);
    }
}
