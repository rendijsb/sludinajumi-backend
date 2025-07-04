<?php

declare(strict_types=1);

namespace App\Http\Resources\Users;

use App\Http\Resources\Roles\RoleResource;
use App\Models\Users\User;
use App\Services\Traits\Resources\ConditionallyLoadFields;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    use ConditionallyLoadFields;

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
            'role' => $this->whenFieldRequired(
                $request,
                'related-role',
                new RoleResource($this->resource->roleRelation()->first())
            ),
            'token' => $this->when(
                $this->shouldIncludeToken($request),
                fn() => $this->resource->createToken('auth_token')->plainTextToken
            ),
        ];
    }

    /**
     * Determine if token should be included in the response
     */
    private function shouldIncludeToken(Request $request): bool
    {
        $authRoutes = ['login', 'register', 'refresh'];
        $currentRoute = $request->route()?->getName() ?? '';

        foreach ($authRoutes as $route) {
            if (str_contains($currentRoute, $route)) {
                return true;
            }
        }

        return $this->requestHasFields($request, 'token', 'fields');
    }
}
