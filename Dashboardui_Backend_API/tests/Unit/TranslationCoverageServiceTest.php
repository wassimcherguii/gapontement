<?php

namespace Tests\Unit;

use App\Models\TranslationDomain;
use App\Models\TranslationKey;
use App\Models\TranslationValue;
use App\Services\TranslationCoverageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationCoverageServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_locale_coverage_percentages(): void
    {
        $domainSlug = 'mobile';
        $domainId = TranslationDomain::query()->where('slug', $domainSlug)->value('id');
        $this->assertNotNull($domainId);

        $k1 = TranslationKey::query()->create([
            'translation_domain_id' => $domainId,
            'key' => 'a.one',
            'status' => 'published',
        ]);
        $k2 = TranslationKey::query()->create([
            'translation_domain_id' => $domainId,
            'key' => 'a.two',
            'status' => 'published',
        ]);
        TranslationKey::query()->create([
            'translation_domain_id' => $domainId,
            'key' => 'draft.only',
            'status' => 'draft',
        ]);

        TranslationValue::query()->create([
            'translation_key_id' => $k1->id,
            'locale' => 'en',
            'value' => 'Hello',
            'status' => 'published',
        ]);
        TranslationValue::query()->create([
            'translation_key_id' => $k2->id,
            'locale' => 'en',
            'value' => ' ',
            'status' => 'published',
        ]);
        TranslationValue::query()->create([
            'translation_key_id' => $k1->id,
            'locale' => 'fr',
            'value' => 'Bonjour',
            'status' => 'published',
        ]);
        TranslationValue::query()->create([
            'translation_key_id' => $k2->id,
            'locale' => 'fr',
            'value' => 'Salut',
            'status' => 'published',
        ]);

        $supported = [
            'en' => ['code' => 'en', 'name' => 'English', 'native' => 'English', 'direction' => 'ltr'],
            'fr' => ['code' => 'fr', 'name' => 'French', 'native' => 'Français', 'direction' => 'ltr'],
        ];

        $service = new TranslationCoverageService;
        $rows = $service->localeCoverage($domainSlug, $supported);

        $byCode = collect($rows)->keyBy('code');

        $this->assertSame(2, $byCode['en']['total']);
        $this->assertSame(1, $byCode['en']['filled']);
        $this->assertSame(50, $byCode['en']['percent']);

        $this->assertSame(2, $byCode['fr']['total']);
        $this->assertSame(2, $byCode['fr']['filled']);
        $this->assertSame(100, $byCode['fr']['percent']);
    }

    public function test_zero_published_keys_returns_null_percent(): void
    {
        $domainSlug = 'web';
        $this->assertNotNull(TranslationDomain::query()->where('slug', $domainSlug)->value('id'));

        $service = new TranslationCoverageService;
        $rows = $service->localeCoverage($domainSlug, [
            'en' => ['code' => 'en', 'name' => 'English', 'native' => 'English', 'direction' => 'ltr'],
        ]);

        $this->assertCount(1, $rows);
        $this->assertSame(0, $rows[0]['total']);
        $this->assertNull($rows[0]['percent']);
    }
}
