<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\BlogPostsSeeder;
use Database\Seeders\LandingHomeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogPostFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_shows_featured_blog_teaser_when_seeded(): void
    {
        $this->seed(LandingHomeSeeder::class);
        $this->seed(BlogPostsSeeder::class);

        $response = $this->get('/en/');

        $response->assertOk();
        $response->assertSee('What’s new at the clinic', false);
        $response->assertSee('/en/blog/welcome-to-our-clinic-blog', false);
    }

    public function test_guest_can_view_published_blog_article(): void
    {
        $this->seed(BlogPostsSeeder::class);

        $response = $this->get('/en/blog/welcome-to-our-clinic-blog');

        $response->assertOk();
        $response->assertSee('What’s new at the clinic', false);
    }

    public function test_admin_can_open_blog_manager(): void
    {
        $this->seed(LandingHomeSeeder::class);
        $this->seed(BlogPostsSeeder::class);
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/en/admin/website/blog');

        $response->assertOk();
        $response->assertSee('welcome-to-our-clinic-blog', false);
    }
}
