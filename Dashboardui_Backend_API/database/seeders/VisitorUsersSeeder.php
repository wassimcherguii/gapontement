<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class VisitorUsersSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Create random patient accounts (e.g. load testing). Password: "password".
     */
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 0; $i < 50; $i++) {
            User::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'role' => 'patient',
                'email_verified_at' => $faker->optional(0.8)->dateTimeBetween('-1 year', 'now'),
                'created_at' => $faker->dateTimeBetween('-2 years', 'now'),
                'updated_at' => now(),
            ]);
        }

        if ($this->command) {
            $this->command->info('Successfully created 50 random patient users.');
        }
    }
}
