<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\TranslationPublishService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationDomainLanguagesCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_public_languages_catalog(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $payload = [
            'domain' => 'mobile',
            'default' => 'en',
            'locale_codes' => ['en', 'fr', 'ar'],
            'locale_name' => [
                'en' => 'English',
                'fr' => 'French',
                'ar' => 'Arabic',
            ],
            'locale_native' => [
                'en' => 'English',
                'fr' => 'Français',
                'ar' => 'العربية',
            ],
            'locale_direction' => [
                'en' => 'ltr',
                'fr' => 'ltr',
                'ar' => 'rtl',
            ],
            'locale_flag' => [
                'en' => '🇺🇸',
                'fr' => '🇫🇷',
                'ar' => '🇸🇦',
            ],
            'locale_active' => [
                'en' => '1',
                'fr' => '1',
                'ar' => '1',
            ],
        ];

        $response = $this->actingAs($admin)
            ->post(route('admin.assets.client-translations.languages-catalog.update', ['lang' => 'en']), $payload);

        $response->assertRedirect();

        $data = app(TranslationPublishService::class)->readLanguages('mobile', false);
        $this->assertSame('en', $data['default']);
        $this->assertArrayHasKey('ar', $data['supported']);
        $this->assertSame('Français', $data['supported']['fr']['native']);
    }
}
