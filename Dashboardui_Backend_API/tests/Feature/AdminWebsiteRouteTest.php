<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminWebsiteRouteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Regression: fallback must not run before {lang} routes (otherwise /fr/admin/... aborts 404).
     */
    public function test_prefixed_admin_website_landing_is_not_404_for_guest(): void
    {
        $response = $this->get('/fr/admin/website/landing');

        $this->assertNotEquals(404, $response->getStatusCode());
        $response->assertRedirect();
    }

    public function test_old_admin_website_home_redirects_to_landing(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/fr/admin/website/home');

        $response->assertRedirect('/fr/admin/website/landing');
    }
}
