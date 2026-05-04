<?php

namespace Tests\Feature;

use App\Models\TranslationKey;
use App\Models\User;
use App\Services\TranslationDomainProvisioner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ClientTranslationAdminKeyUpdateTest extends TestCase
{
    use RefreshDatabase;

    private string $slug = 'admkeyupdtest';

    protected function tearDown(): void
    {
        $base = base_path('jsonassets');
        $pl = $base.'/public_languages_'.$this->slug.'.json';
        if (File::exists($pl)) {
            File::delete($pl);
        }
        $dir = $base.'/i18n/'.$this->slug;
        if (File::isDirectory($dir)) {
            File::deleteDirectory($dir);
        }

        parent::tearDown();
    }

    public function test_admin_can_update_key_from_db_modal_flow(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        app(TranslationDomainProvisioner::class)->provision($this->slug, 'Admin key update test');

        $domainId = \App\Models\TranslationDomain::query()->where('slug', $this->slug)->value('id');
        $key = TranslationKey::query()->create([
            'translation_domain_id' => $domainId,
            'key' => 'modal.test.key',
            'status' => 'published',
            'description' => 'old',
        ]);

        $response = $this->actingAs($admin)->put(
            route('admin.assets.client-translations.keys.update', ['lang' => 'en', 'translation_key' => $key->id]),
            [
                'domain' => $this->slug,
                'description' => 'new desc',
                'status' => 'published',
                'values' => [
                    'en' => 'Hello modal',
                ],
            ]
        );

        $response->assertRedirect();
        $key->refresh();
        $this->assertSame('new desc', $key->description);
        $this->assertSame('Hello modal', $key->values()->where('locale', 'en')->value('value'));
    }
}
