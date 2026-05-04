<?php

namespace Tests\Feature;

use App\Services\LandingPagePublishService;
use Database\Seeders\LandingHomeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WelcomeLandingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(LandingHomeSeeder::class);
        app(LandingPagePublishService::class)->publish('home');
    }

    public function test_welcome_english_shows_published_headline_from_json(): void
    {
        $response = $this->get('/en/');

        $response->assertOk();
        $response->assertSee(LandingHomeSeeder::HERO_HEADLINE_EN, false);
    }

    public function test_welcome_french_shows_published_headline_from_json(): void
    {
        $response = $this->get('/fr/');

        $response->assertOk();
        $response->assertSee(LandingHomeSeeder::HERO_HEADLINE_FR, false);
    }
}
