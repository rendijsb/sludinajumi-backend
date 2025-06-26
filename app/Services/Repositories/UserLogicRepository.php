<?php

declare(strict_types=1);

namespace App\Services\Repositories;

use App\Http\DataTransferObjects\Auth\RegisterRequestData;
use App\Models\Users\User;

class UserLogicRepository
{
    public function __construct(
        private readonly UserDbRepository $userDbRepository
    )
    {
    }

    public function register(RegisterRequestData $data): User
    {
        $payload = [
            User::NAME => $data->name
        ];

        return $this->userDbRepository->register($payload);
    }
}
