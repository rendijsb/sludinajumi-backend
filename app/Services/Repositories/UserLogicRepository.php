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
        private UserDbRepository $userDbRepository
    )
    {
    }

    public function register(RegisterRequestData $data): User
    {
        $payload = [
            User::NAME => $data->name,
            User::EMAIL => $data->email,
            User::PHONE => $data->phone,
            User::PASSWORD => Hash::make($data->password),
            User::ROLE_ID => Role::where('name', 'user')->first()?->id ?? 2,
            User::IS_ACTIVE => true,
        ];

        return $this->userDbRepository->createUser($payload);
    }
}
