<?php

declare(strict_types=1);

namespace App\Services\Repositories;

use App\Models\Users\User;

class UserDbRepository
{
    public function __construct(
        private readonly User $user
    )
    {
    }

    public function createUser(array $payload): User
    {
        return $this->user->newQuery()->create($payload);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->user->newQuery()->where(User::EMAIL, $email)->first();
    }

    public function findById(int $id): ?User
    {
        return $this->user->newQuery()->find($id);
    }

    public function updateUser(User $user, array $payload): User
    {
        $user->update($payload);
        return $user->fresh();
    }
}
