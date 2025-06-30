<?php

declare(strict_types=1);

namespace App\Services\Repositories;

use App\Http\DataTransferObjects\Auth\RegisterRequestData;
use App\Models\Roles\Role;
use App\Models\Users\User;
use Illuminate\Support\Facades\Hash;

readonly class UserLogicRepository
{
    public function __construct(
        private readonly UserDbRepository $userDbRepository,
        private readonly Role $roleModel
    ) {
    }

    public function register(RegisterRequestData $data): User
    {
        $defaultRole = $this->roleModel->where(Role::NAME, 'user')->first();

        $payload = [
            User::NAME => $data->name,
            User::EMAIL => $data->email,
            User::PHONE => $data->phone,
            User::PASSWORD => Hash::make($data->password),
            User::ROLE_ID => $defaultRole?->getId() ?? 2,
            User::IS_ACTIVE => true,
        ];

        return $this->userDbRepository->createUser($payload);
    }
}
