<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::query()
            ->with('translations')
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.website.blog.index', [
            'posts' => $posts,
            'titleKey' => 'website_blog',
        ]);
    }

    public function create(): View
    {
        return view('admin.website.blog.form', [
            'post' => new BlogPost([
                'status' => BlogPost::STATUS_DRAFT,
                'is_featured' => false,
                'views_count' => 0,
                'likes_count' => 0,
                'saves_count' => 0,
                'sort_order' => 0,
                'images' => [],
            ]),
            'titleKey' => 'blog_admin_create',
            'locales' => array_keys(get_supported_languages()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $locales = array_keys(get_supported_languages());
        $validated = $this->validatePost($request, null, $locales);
        $images = $this->parseImages($request->input('images_raw'));

        $post = DB::transaction(function () use ($validated, $images, $locales, $request) {
            $post = BlogPost::query()->create([
                'slug' => $validated['slug'],
                'status' => $validated['status'],
                'is_featured' => $request->boolean('is_featured'),
                'views_count' => max(0, (int) $validated['views_count']),
                'likes_count' => max(0, (int) $validated['likes_count']),
                'saves_count' => max(0, (int) $validated['saves_count']),
                'sort_order' => max(0, min(65535, (int) $validated['sort_order'])),
                'published_at' => $validated['published_at'] ?? null,
                'images' => $images,
            ]);

            foreach ($locales as $loc) {
                $row = $validated['content'][$loc] ?? [];
                $post->translations()->create([
                    'locale' => $loc,
                    'title' => $row['title'] ?? '',
                    'excerpt' => $row['excerpt'] ?? null,
                    'body' => $row['body'] ?? null,
                ]);
            }

            return $post;
        });

        return redirect()
            ->route('admin.website.blog.edit', ['lang' => app()->getLocale(), 'blog_post' => $post->id])
            ->with('status', get_translation('blog_admin_saved'));
    }

    public function edit(BlogPost $blog_post): View
    {
        $blog_post->load('translations');

        return view('admin.website.blog.form', [
            'post' => $blog_post,
            'titleKey' => 'blog_admin_edit',
            'locales' => array_keys(get_supported_languages()),
        ]);
    }

    public function update(Request $request, BlogPost $blog_post): RedirectResponse
    {
        $locales = array_keys(get_supported_languages());
        $validated = $this->validatePost($request, $blog_post->id, $locales);
        $images = $this->parseImages($request->input('images_raw'));

        DB::transaction(function () use ($blog_post, $validated, $images, $locales, $request) {
            $blog_post->update([
                'slug' => $validated['slug'],
                'status' => $validated['status'],
                'is_featured' => $request->boolean('is_featured'),
                'views_count' => max(0, (int) $validated['views_count']),
                'likes_count' => max(0, (int) $validated['likes_count']),
                'saves_count' => max(0, (int) $validated['saves_count']),
                'sort_order' => max(0, min(65535, (int) $validated['sort_order'])),
                'published_at' => $validated['published_at'] ?? null,
                'images' => $images,
            ]);

            foreach ($locales as $loc) {
                $row = $validated['content'][$loc] ?? [];
                BlogPostTranslation::query()->updateOrCreate(
                    [
                        'blog_post_id' => $blog_post->id,
                        'locale' => $loc,
                    ],
                    [
                        'title' => $row['title'] ?? '',
                        'excerpt' => $row['excerpt'] ?? null,
                        'body' => $row['body'] ?? null,
                    ]
                );
            }
        });

        return redirect()
            ->route('admin.website.blog.edit', ['lang' => app()->getLocale(), 'blog_post' => $blog_post->id])
            ->with('status', get_translation('blog_admin_saved'));
    }

    public function destroy(BlogPost $blog_post): RedirectResponse
    {
        $blog_post->delete();

        return redirect()
            ->route('admin.website.blog', ['lang' => app()->getLocale()])
            ->with('status', get_translation('blog_admin_deleted'));
    }

    /**
     * @param  list<string>  $locales
     * @return array<string, mixed>
     */
    private function validatePost(Request $request, ?int $ignoreId, array $locales): array
    {
        $slugRule = 'required|string|max:160|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/';
        $slugRule .= $ignoreId
            ? '|unique:blog_posts,slug,'.$ignoreId
            : '|unique:blog_posts,slug';

        $rules = [
            'slug' => $slugRule,
            'status' => 'required|string|in:'.BlogPost::STATUS_DRAFT.','.BlogPost::STATUS_PUBLISHED.','.BlogPost::STATUS_ARCHIVED,
            'is_featured' => 'nullable|boolean',
            'views_count' => 'nullable|integer|min:0|max:4294967295',
            'likes_count' => 'nullable|integer|min:0|max:4294967295',
            'saves_count' => 'nullable|integer|min:0|max:4294967295',
            'sort_order' => 'nullable|integer|min:0|max:65535',
            'published_at' => 'nullable|date',
            'images_raw' => 'nullable|string|max:20000',
            'content' => 'required|array',
        ];

        foreach ($locales as $loc) {
            $rules['content.'.$loc.'.title'] = 'required|string|max:512';
            $rules['content.'.$loc.'.excerpt'] = 'nullable|string|max:10000';
            $rules['content.'.$loc.'.body'] = 'nullable|string|max:500000';
        }

        return $request->validate($rules);
    }

    /**
     * @return list<string>
     */
    private function parseImages(?string $raw): array
    {
        if ($raw === null || trim($raw) === '') {
            return [];
        }
        $parts = preg_split('/\r\n|\r|\n|,/', $raw);
        $out = [];
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part !== '') {
                $out[] = $part;
            }
        }
        $out = array_values(array_unique($out));

        return array_slice($out, 0, 20);
    }
}
