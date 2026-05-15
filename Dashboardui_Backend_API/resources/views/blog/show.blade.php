@extends('layouts.public-article')

@section('page_title', $tr?->title ?? 'Blog')

@section('content')
@if (! $tr || $tr->title === '')
    <p class="text-sm" style="color: var(--lp-muted);">{{ get_translation('blog_public_missing_locale') }}</p>
@else
    <article>
        <h1 class="text-3xl sm:text-4xl font-bold tracking-tight mb-4" style="color: var(--lp-text);">{{ $tr->title }}</h1>
        @if ($post->published_at)
            <p class="text-sm mb-8" style="color: var(--lp-muted);">{{ $post->published_at->translatedFormat('d M Y H:i') }}</p>
        @endif
        @if (is_array($post->images) && count($post->images) > 0)
            <div class="flex flex-col gap-4 mb-8">
                @foreach ($post->images as $src)
                    <img src="{{ Str::startsWith($src, ['http://', 'https://']) ? $src : asset($src) }}" alt="" class="w-full rounded-xl border object-cover max-h-96" style="border-color: var(--lp-border);">
                @endforeach
            </div>
        @endif
        @if (filled($tr->excerpt))
            <p class="text-lg mb-8 font-medium" style="color: var(--lp-muted);">{{ $tr->excerpt }}</p>
        @endif
        <div class="blog-prose text-base space-y-4" style="color: var(--lp-text);">
            {!! $tr->body !!}
        </div>
    </article>
@endif
@endsection
