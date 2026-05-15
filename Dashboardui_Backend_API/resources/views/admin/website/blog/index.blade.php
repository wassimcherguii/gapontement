@extends('layouts.admin')

@section('title', get_translation('website_blog'))
@section('description', get_translation('blog_admin_intro'))

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
            {{ get_translation('website_blog') }}
        </h1>
        <p class="mt-2 text-sm max-w-2xl {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
            {{ get_translation('blog_admin_intro') }}
        </p>
    </div>
    <a href="{{ route('admin.website.blog.create', ['lang' => app()->getLocale()]) }}"
       class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-semibold text-white transition hover:brightness-105"
       style="background: var(--primary-color, #0F4C81);">
        {{ get_translation('blog_admin_new') }}
    </a>
</div>

@if (session('status'))
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm" style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">
        {{ session('status') }}
    </div>
@endif

<div class="admin-card rounded-xl overflow-hidden border" style="background: var(--surface-color); border-color: var(--border-color);">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b" style="border-color: var(--border-color);">
                    <th class="px-4 py-3 font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('blog_admin_col_slug') }}</th>
                    <th class="px-4 py-3 font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('blog_admin_col_status') }}</th>
                    <th class="px-4 py-3 font-semibold text-center" style="color: var(--text-color);">{{ get_translation('blog_admin_col_featured') }}</th>
                    <th class="px-4 py-3 font-semibold text-center" style="color: var(--text-color);">{{ get_translation('blog_admin_col_views') }}</th>
                    <th class="px-4 py-3 font-semibold text-center" style="color: var(--text-color);">{{ get_translation('blog_admin_col_likes') }}</th>
                    <th class="px-4 py-3 font-semibold text-center" style="color: var(--text-color);">{{ get_translation('blog_admin_col_saves') }}</th>
                    <th class="px-4 py-3 font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-left' : 'text-right' }}" style="color: var(--text-color);">{{ get_translation('blog_admin_col_actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($posts as $post)
                    <tr class="border-b" style="border-color: var(--border-color);">
                        <td class="px-4 py-3 font-mono text-xs" style="color: var(--text-secondary-color);">{{ $post->slug }}</td>
                        <td class="px-4 py-3" style="color: var(--text-color);">{{ get_translation('blog_status_'.$post->status) }}</td>
                        <td class="px-4 py-3 text-center">{{ $post->is_featured ? '✓' : '—' }}</td>
                        <td class="px-4 py-3 text-center tabular-nums">{{ number_format($post->views_count) }}</td>
                        <td class="px-4 py-3 text-center tabular-nums">{{ number_format($post->likes_count) }}</td>
                        <td class="px-4 py-3 text-center tabular-nums">{{ number_format($post->saves_count) }}</td>
                        <td class="px-4 py-3 {{ is_rtl_language(app()->getLocale()) ? 'text-left' : 'text-right' }}">
                            <a href="{{ route('admin.website.blog.edit', ['lang' => app()->getLocale(), 'blog_post' => $post->id]) }}" class="text-sm font-semibold hover:underline" style="color: var(--primary-color, #0F4C81);">{{ get_translation('edit') }}</a>
                            <form method="post" action="{{ route('admin.website.blog.destroy', ['lang' => app()->getLocale(), 'blog_post' => $post->id]) }}" class="inline ms-2" onsubmit="return confirm(@json(get_translation('blog_admin_delete_confirm')));">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-semibold hover:underline text-red-600">{{ get_translation('delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-sm" style="color: var(--text-secondary-color);">{{ get_translation('blog_admin_empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($posts->hasPages())
        <div class="px-4 py-3 border-t" style="border-color: var(--border-color);">
            {{ $posts->links() }}
        </div>
    @endif
</div>
@endsection
