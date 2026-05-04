<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTranslationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_languages_endpoint_returns_supported_locales(): void
    {
        $response = $this->getJson('/api/v1/i18n/mobile/languages');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'default',
                    'supported',
                ],
            ]);

        $supported = $response->json('data.supported');
        $this->assertArrayHasKey('en', $supported);
        $this->assertArrayHasKey('fr', $supported);
        $this->assertArrayHasKey('ar', $supported);
    }

    public function test_mobile_bundle_returns_nested_strings(): void
    {
        $response = $this->getJson('/api/v1/i18n/mobile/en');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.locale', 'en')
            ->assertJsonPath('data.domain', 'mobile')
            ->assertJsonPath('data.bundle.tabs.home', 'Home');
    }

    public function test_invalid_domain_is_not_routable(): void
    {
        $response = $this->getJson('/api/v1/i18n/unknown/languages');

        $response->assertNotFound();
    }

    public function test_unsupported_locale_returns_422(): void
    {
        $response = $this->getJson('/api/v1/i18n/mobile/xx');

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }
}
