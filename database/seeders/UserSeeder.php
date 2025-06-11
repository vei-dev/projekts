<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole);

        // Create dispatcher user
        $dispatcher = User::create([
            'name' => 'Dispatcher User',
            'email' => 'dispatcher@example.com',
            'password' => Hash::make('password'),
        ]);

        $dispatcherRole = Role::where('name', 'dispatcher')->first();
        $dispatcher->roles()->attach($dispatcherRole);

        // Create worker user
        $worker = User::create([
            'name' => 'Worker User',
            'email' => 'worker@example.com',
            'password' => Hash::make('password'),
        ]);

        $workerRole = Role::where('name', 'worker')->first();
        $worker->roles()->attach($workerRole);
    }
} 