<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LogoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Logo::create([
            'name' => 'Main Logo',
            'filename' => 'ClientLogo.png',
            'path' => 'assets/logos/ClientLogo.png',
            'alt' => 'Technodec Logo',
            'description' => 'Main company logo used in headers, sidebars, and branding'
        ]);

        \App\Models\Logo::create([
            'name' => 'Favicon',
            'filename' => 'favicon.png',
            'path' => 'favicon.png',
            'alt' => 'Technodec Favicon',
            'description' => 'Browser tab icon and favicon'
        ]);
    }
}
