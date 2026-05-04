<?php

namespace Tests\Feature;

use App\Models\TranslationKey;
use App\Models\TranslationValue;
use App\Models\User;
use App\Services\TranslationDomainProvisioner;
use App\Services\TranslationPublishService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ClientTranslationSyncDiffTest extends TestCase
{
    use RefreshDatabase;

    private string $slug = 'syncdifftest';

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

    public function test_guest_cannot_access_sync_diff(): void
    {
        app(TranslationDomainProvisioner::class)->provision($this->slug, 'Sync diff test');

        $this->get(route('admin.assets.client-translations.sync-diff', [
            'lang' => 'en',
            'domain' => $this->slug,
        ]))->assertRedirect();
    }

    public function test_sync_diff_reports_publish_drift_when_json_not_exported(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        app(TranslationDomainProvisioner::class)->provision($this->slug, 'Sync diff test');

        $domainId = \App\Models\TranslationDomain::query()->where('slug', $this->slug)->value('id');
        $key = TranslationKey::query()->create([
            'translation_domain_id' => $domainId,
            'key' => 'drift.only.db',
            'status' => 'published',
        ]);
        TranslationValue::query()->create([
            'translation_key_id' => $key->id,
            'locale' => 'en',
            'value' => 'Only in database',
            'status' => 'published',
        ]);

        $response = $this->actingAs($admin)->getJson(route('admin.assets.client-translations.sync-diff', [
            'lang' => 'en',
            'domain' => $this->slug,
        ]));

        $response->assertOk()
            ->assertJsonPath('publish.count', 1)
            ->assertJsonStructure([
                'publish' => ['count', 'samples'],
                'import' => ['count', 'samples'],
                'meta_checksum_match',
                'has_meta_checksum',
            ]);
    }

    public function test_sync_diff_zero_when_exported_aligned(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        app(TranslationDomainProvisioner::class)->provision($this->slug, 'Sync diff test');

        $domainId = \App\Models\TranslationDomain::query()->where('slug', $this->slug)->value('id');
        $key = TranslationKey::query()->create([
            'translation_domain_id' => $domainId,
            'key' => 'aligned.key',
            'status' => 'published',
        ]);
        TranslationValue::query()->create([
            'translation_key_id' => $key->id,
            'locale' => 'en',
            'value' => 'Aligned value',
            'status' => 'published',
        ]);

        app(TranslationPublishService::class)->exportDomain($this->slug);

        $response = $this->actingAs($admin)->getJson(route('admin.assets.client-translations.sync-diff', [
            'lang' => 'en',
            'domain' => $this->slug,
        ]));

        $response->assertOk()
            ->assertJsonPath('publish.count', 0)
            ->assertJsonPath('import.count', 0);
    }
}
