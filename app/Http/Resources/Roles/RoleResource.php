<?php

declare(strict_types=1);

namespace App\Http\Resources\Roles;

use App\Models\Roles\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /** @var Role $resource */
    public $resource;

    protected array $conditionalFields = [
        'name' => Role::NAME,
        'display_name' => Role::DISPLAY_NAME,
        'description' => Role::DESCRIPTION,
    ];

    public function getData(Request $request): array
    {
        return [
            'id' => $this->resource->getId(),
        ];
    }
}
