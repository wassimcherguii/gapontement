<?php

namespace Tests\Feature;

use App\Models\LandingPage;
use App\Models\LandingSection;
use App\Models\User;
use App\Services\LandingPagePublishService;
use Database\Seeders\LandingHomeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingJsonImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_json_updates_hero_in_database(): void
    {
        $this->seed(LandingHomeSeeder::class);
        $publisher = app(LandingPagePublishService::class);
        $publisher->publish('home');

        $page = LandingPage::query()->where('slug', 'home')->firstOrFail();
        $bundle = $publisher->composeLocaleBundle($page, 'en');
        $bundle['hero']['headline'] = 'JSON_IMPORT_HEADLINE_XYZ';

        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/en/admin/website/landing/import-json', [
            'locale' => 'en',
            'json_payload' => json_encode($bundle, JSON_THROW_ON_ERROR),
        ]);

        $response->assertRedirect();
        $this->assertStringContainsString('tab=json', $response->headers->get('Location') ?? '');

        $hero = LandingSection::query()
            ->where('landing_page_id', $page->id)
            ->where('section_key', 'hero')
            ->firstOrFail();
        $content = $hero->translations()->where('locale', 'en')->value('content');
        $this->assertIsArray($content);
        $this->assertSame('JSON_IMPORT_HEADLINE_XYZ', $content['headline'] ?? null);
    }
}
