<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles
        $this->call([
            RoleSeeder::class,
        ]);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // Assign admin role
        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole);

        // Create dispatcher user
        $dispatcherRole = Role::where('name', 'dispatcher')->first();
        $dispatcher = User::create([
            'name' => 'Dispatcher',
            'email' => 'abc.ak@gmail.com',
            'password' => Hash::make('password'),
        ]);
        $dispatcher->roles()->attach($dispatcherRole);

        // Create worker user
        $workerRole = Role::where('name', 'worker')->first();
        $worker = User::create([
            'name' => 'Worker',
            'email' => 'worker@example.com',
            'password' => Hash::make('password'),
        ]);
        $worker->roles()->attach($workerRole);
    }
}

