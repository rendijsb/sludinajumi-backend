<?php

declare(strict_types=1);

namespace App\Http\Resources\Users;

use App\Http\Resources\Roles\RoleResource;
use App\Models\Users\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /** @var User $resource */
    public $resource;

    protected array $conditionalFields = [
        'name' => User::NAME,
        'email' => User::EMAIL,
        'phone' => User::PHONE,
        'is_active' => User::IS_ACTIVE,
        'email_verified_at' => User::EMAIL_VERIFIED_AT,
        'phone_verified_at' => User::PHONE_VERIFIED_AT,
        'created_at' => User::CREATED_AT,
        'updated_at' => User::UPDATED_AT,
    ];

    protected function getData(Request $request): array
    {
        return [
            'id' => $this->resource->getId(),
            'role' => new RoleResource($this->whenLoaded('roleRelation')),
        ];
    }
}
