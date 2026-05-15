<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            UserRoleSeeder::class,
            AppointmentCoreSeeder::class,
            ColorPaletteSeeder::class,
            LogoSeeder::class,
            TranslationDomainSeeder::class,
            LandingHomeSeeder::class,
            BlogPostsSeeder::class,
            // VisitorUsersSeeder::class, // Uncomment to seed 50 random patient users
        ]);

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'role' => 'patient',
                'email_verified_at' => now(),
            ]
        );
    }
}
