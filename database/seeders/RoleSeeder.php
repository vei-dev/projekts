<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'description' => 'Administrator'],
            ['name' => 'dispatcher', 'description' => 'Dispatcher'],
            ['name' => 'worker', 'description' => 'Worker'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // Assign admin role to the first user
        $user = User::first();
        if ($user) {
            $adminRole = Role::where('name', 'admin')->first();
            $user->roles()->attach($adminRole);
        }
    }
} 