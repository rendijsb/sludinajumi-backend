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


}
