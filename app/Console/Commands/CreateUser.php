<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Command
{
    protected $signature = 'user:create {name} {email} {password} {role}';
    protected $description = 'Create a new user with a role';

    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');
        $roleName = $this->argument('role');

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            $this->error("Role {$roleName} not found.");
            return 1;
        }

        $user->roles()->attach($role);
        $this->info("User {$name} created with role {$roleName} successfully.");
        return 0;
    }
} 