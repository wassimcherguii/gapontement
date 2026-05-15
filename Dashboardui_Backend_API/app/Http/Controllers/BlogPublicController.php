<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\View\View;

class BlogPublicController extends Controller
{
    public function show(string $lang, string $slug): View
    {
        $post = BlogPost::query()
            ->published()
            ->where('slug', $slug)
            ->with('translations')
            ->firstOrFail();

        $post->increment('views_count');
        $post->refresh();

        $tr = $post->translation($lang);

        return view('blog.show', [
            'post' => $post,
            'tr' => $tr,
        ]);
    }
}
