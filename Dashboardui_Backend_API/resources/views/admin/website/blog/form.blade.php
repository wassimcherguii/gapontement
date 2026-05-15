@extends('layouts.admin')

@section('title', get_translation($titleKey))
@section('description', get_translation('blog_admin_intro'))

@section('content')
@php
    $localeLabel = function (string $loc): string {
        $info = get_language_info($loc) ?? [];
        return (string) ($info['native'] ?? $info['name'] ?? strtoupper($loc));
    };
    $imagesRaw = old('images_raw', ($post->exists && is_array($post->images) && $post->images !== []) ? implode("\n", $post->images) : '');
@endphp

<div class="mb-6">
    <a href="{{ route('admin.website.blog', ['lang' => app()->getLocale()]) }}" class="text-sm font-medium hover:underline" style="color: var(--primary-color, #0F4C81);">← {{ get_translation('website_blog') }}</a>
    <h1 class="mt-3 text-2xl sm:text-3xl font-bold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">
        {{ get_translation($titleKey) }}
    </h1>
</div>

@if ($errors->any())
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm text-red-700" style="border-color: var(--border-color); background: color-mix(in srgb, red 8%, transparent);">
        <ul class="list-disc ms-4 space-y-1">
            @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if (session('status'))
    <div class="mb-4 rounded-lg border px-4 py-3 text-sm" style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">
        {{ session('status') }}
    </div>
@endif

<form id="blog-post-form" method="post"
      action="{{ $post->exists ? route('admin.website.blog.update', ['lang' => app()->getLocale(), 'blog_post' => $post->id]) : route('admin.website.blog.store', ['lang' => app()->getLocale()]) }}"
      class="space-y-6">
    @csrf
    @if ($post->exists)
        @method('PUT')
    @endif

    <div class="admin-card rounded-xl p-6 sm:p-8 space-y-4 border" style="background: var(--surface-color); border-color: var(--border-color);">
        <h2 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('blog_admin_section_meta') }}</h2>
        <div class="grid gap-4 sm:grid-cols-2">
            <label class="block text-sm sm:col-span-2">
                <span class="block mb-1 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ get_translation('blog_admin_slug') }}</span>
                <input type="text" name="slug" value="{{ old('slug', $post->slug) }}" required
                       pattern="[a-z0-9]+(?:-[a-z0-9]+)*"
                       class="w-full rounded-lg border px-3 py-2 text-sm font-mono" style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);"
                       placeholder="my-post-slug">
            </label>
            <label class="block text-sm">
                <span class="block mb-1 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ get_translation('blog_admin_status') }}</span>
                <select name="status" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">
                    @foreach (['draft', 'published', 'archived'] as $st)
                        <option value="{{ $st }}" @selected(old('status', $post->status) === $st)>{{ get_translation('blog_status_'.$st) }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm">
                <span class="block mb-1 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ get_translation('blog_admin_published_at') }}</span>
                <input type="datetime-local" name="published_at"
                       value="{{ old('published_at', $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}"
                       class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">
            </label>
            <label class="inline-flex items-center gap-2 text-sm sm:col-span-2 {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" value="1" class="rounded border" @checked(filter_var(old('is_featured', $post->exists ? $post->is_featured : false), FILTER_VALIDATE_BOOLEAN))>
                <span style="color: var(--text-color);">{{ get_translation('blog_admin_featured_landing') }}</span>
            </label>
            <label class="block text-sm">
                <span class="block mb-1 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ get_translation('blog_admin_sort_order') }}</span>
                <input type="number" name="sort_order" min="0" max="65535" value="{{ old('sort_order', $post->sort_order) }}"
                       class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color); background: var(--surface-color); color: var(--text-color);">
            </label>
        </div>
    </div>

    <div class="admin-card rounded-xl p-6 sm:p-8 space-y-4 border" style="background: var(--surface-color); border-color: var(--border-color);">
        <h2 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('blog_admin_section_stats') }}</h2>
        <p class="text-xs {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ get_translation('blog_admin_stats_hint') }}</p>
        <div class="grid gap-4 sm:grid-cols-3">
            <label class="block text-sm">
                <span class="block mb-1 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ get_translation('blog_admin_col_views') }}</span>
                <input type="number" name="views_count" min="0" value="{{ old('views_count', $post->views_count) }}" class="w-full rounded-lg border px-3 py-2 text-sm tabular-nums" style="border-color: var(--border-color);">
            </label>
            <label class="block text-sm">
                <span class="block mb-1 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ get_translation('blog_admin_col_likes') }}</span>
                <input type="number" name="likes_count" min="0" value="{{ old('likes_count', $post->likes_count) }}" class="w-full rounded-lg border px-3 py-2 text-sm tabular-nums" style="border-color: var(--border-color);">
            </label>
            <label class="block text-sm">
                <span class="block mb-1 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ get_translation('blog_admin_col_saves') }}</span>
                <input type="number" name="saves_count" min="0" value="{{ old('saves_count', $post->saves_count) }}" class="w-full rounded-lg border px-3 py-2 text-sm tabular-nums" style="border-color: var(--border-color);">
            </label>
        </div>
    </div>

    <div class="admin-card rounded-xl p-6 sm:p-8 space-y-4 border" style="background: var(--surface-color); border-color: var(--border-color);">
        <h2 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('blog_admin_section_images') }}</h2>
        <p class="text-xs {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ get_translation('blog_admin_images_hint') }}</p>
        <label class="block text-sm">
            <textarea name="images_raw" rows="4" dir="ltr" class="w-full rounded-lg border px-3 py-2 text-sm font-mono" style="border-color: var(--border-color);">{{ $imagesRaw }}</textarea>
        </label>
    </div>

    @foreach ($locales as $loc)
        @php
            $tr = $post->translations->firstWhere('locale', $loc);
        @endphp
        <div class="admin-card rounded-xl p-6 sm:p-8 space-y-4 border" style="background: var(--surface-color); border-color: var(--border-color);">
            <h2 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ $localeLabel($loc) }}</h2>
            <label class="block text-sm">
                <span class="block mb-1 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ get_translation('blog_admin_title') }}</span>
                <input type="text" name="content[{{ $loc }}][title]" required
                       value="{{ old('content.'.$loc.'.title', $tr->title ?? '') }}"
                       class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);">
            </label>
            <label class="block text-sm">
                <span class="block mb-1 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ get_translation('blog_admin_excerpt') }}</span>
                <textarea name="content[{{ $loc }}][excerpt]" rows="3" class="w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);">{{ old('content.'.$loc.'.excerpt', $tr->excerpt ?? '') }}</textarea>
            </label>
            <label class="block text-sm">
                <span class="block mb-1 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ get_translation('blog_admin_body') }}</span>
                <textarea id="blog-body-{{ $loc }}" name="content[{{ $loc }}][body]" rows="12" class="blog-wysiwyg w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);">{{ old('content.'.$loc.'.body', $tr->body ?? '') }}</textarea>
            </label>
        </div>
    @endforeach

    <div class="flex flex-wrap gap-3 {{ is_rtl_language(app()->getLocale()) ? 'justify-end' : 'justify-start' }}">
        <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition hover:brightness-105" style="background: var(--primary-color, #0F4C81);">
            {{ get_translation('save') }}
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
<script>
(function () {
    if (typeof tinymce === 'undefined') return;
    tinymce.init({
        selector: 'textarea.blog-wysiwyg',
        height: 380,
        menubar: false,
        plugins: 'link lists code table autoresize',
        toolbar: 'undo redo | blocks | bold italic underline | link bullist numlist | table | code removeformat',
        branding: false,
        promotion: false,
        relative_urls: false,
        remove_script_host: false,
        convert_urls: true,
        license_key: 'gpl'
    });
    var form = document.getElementById('blog-post-form');
    if (form) {
        form.addEventListener('submit', function () {
            if (typeof tinymce !== 'undefined') {
                tinymce.triggerSave();
            }
        });
    }
})();
</script>
@endpush
