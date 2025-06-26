<?php

declare(strict_types=1);

namespace App\Models\Roles;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    public const ID = 'id';
    public const NAME = 'name';
    public const DISPLAY_NAME = 'display_name';
    public const DESCRIPTION = 'description';

    protected $fillable = [
        self::NAME,
        self::DISPLAY_NAME,
        self::DESCRIPTION,
    ];

    public function usersRelation(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function getId(): int
    {
        return $this->getAttribute(self::ID);
    }
}
