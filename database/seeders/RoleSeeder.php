<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Roles\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrators',
                'description' => 'System administrator with full access',
            ],
            [
                'name' => 'user',
                'display_name' => 'Lietotājs',
                'description' => 'Regular user who can create advertisements',
            ],
            [
                'name' => 'moderator',
                'display_name' => 'Moderators',
                'description' => 'Moderator who can review advertisements',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}
