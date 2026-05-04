<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * One seeded user per role (local / staging). Password for all: "password".
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@example.com',
                'password' => bcrypt('password'),
                'role' => 'superadmin',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Dr. Sample',
                'email' => 'doctor@example.com',
                'password' => bcrypt('password'),
                'role' => 'doctor',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Secretary User',
                'email' => 'secretary@example.com',
                'password' => bcrypt('password'),
                'role' => 'secretary',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Patient User',
                'email' => 'patient@example.com',
                'password' => bcrypt('password'),
                'role' => 'patient',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Companion User',
                'email' => 'companion@example.com',
                'password' => bcrypt('password'),
                'role' => 'companion',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
