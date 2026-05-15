<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use Illuminate\Database\Seeder;

class BlogPostsSeeder extends Seeder
{
    public function run(): void
    {
        $post = BlogPost::query()->firstOrCreate(
            ['slug' => 'welcome-to-our-clinic-blog'],
            [
                'status' => BlogPost::STATUS_PUBLISHED,
                'is_featured' => true,
                'views_count' => 0,
                'likes_count' => 0,
                'saves_count' => 0,
                'sort_order' => 0,
                'published_at' => now()->subDay(),
                'images' => [],
            ]
        );

        $rows = [
            'en' => [
                'title' => 'What’s new at the clinic',
                'excerpt' => 'Product updates and tips for running a smoother front desk.',
                'body' => '<p>We are excited to share regular updates about appointments, reminders, and best practices for your team.</p>',
            ],
            'fr' => [
                'title' => 'Nouveautés au cabinet',
                'excerpt' => 'Mises à jour produit et conseils pour l’accueil.',
                'body' => '<p>Nous partageons ici des nouvelles sur les rendez-vous et l’organisation du cabinet.</p>',
            ],
            'ar' => [
                'title' => 'جديد العيادة',
                'excerpt' => 'تحديثات ونصائح لاستقبال أفضل.',
                'body' => '<p>نشارك هنا أخبار المواعيد وأفضل الممارسات لفريقكم.</p>',
            ],
        ];

        foreach ($rows as $loc => $row) {
            BlogPostTranslation::query()->updateOrCreate(
                ['blog_post_id' => $post->id, 'locale' => $loc],
                $row
            );
        }
    }
}
