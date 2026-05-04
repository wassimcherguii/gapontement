<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class TranslationDomainProvisionerTest extends TestCase
{
    use RefreshDatabase;

    private string $slug = 'ax9provtest';

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

    public function test_admin_can_provision_domain_files_and_row(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(
            route('admin.assets.translation-domains.store', ['lang' => 'en']),
            ['slug' => $this->slug, 'name' => 'Provision test']
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('translation_domains', ['slug' => $this->slug, 'name' => 'Provision test']);
        $this->assertFileExists(base_path('jsonassets/public_languages_'.$this->slug.'.json'));
        $this->assertTrue(File::isDirectory(base_path('jsonassets/i18n/'.$this->slug)));
    }
}
