{{-- Structured fields for one locale bundle (page-cache JSON shape). Inputs use data-json-path (camelCase); JS keeps textarea in sync. --}}
@php
    /** @var string $loc */
    /** @var array<string, mixed> $bundle */
    $jb = fn (string $path) => data_get($bundle, $path);
@endphp

<div class="landing-json-structured space-y-8">
    <p class="text-xs font-medium {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
        {{ get_translation('landing_cms_json_form_intro') }}
    </p>

    <div class="admin-card rounded-xl p-6 sm:p-8 space-y-4" style="background: var(--surface-color); border: 1px solid var(--border-color);">
        <h2 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('landing_cms_seo_section') }} ({{ $localeLabel($loc) }})</h2>
        <div class="grid gap-3 sm:grid-cols-2">
            <label class="block text-sm">
                <span class="block mb-1 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ $cmsField('meta_title') }}</span>
                <input type="text" class="landing-json-field w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"
                       data-json-path="meta.title" value="{{ $jb('meta.title') }}">
            </label>
            <label class="block text-sm sm:col-span-2">
                <span class="block mb-1 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ $cmsField('meta_description') }}</span>
                <textarea rows="2" class="landing-json-field w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"
                          data-json-path="meta.description">{{ $jb('meta.description') }}</textarea>
            </label>
        </div>
    </div>

    <div class="admin-card rounded-xl p-6 sm:p-8 space-y-4" style="background: var(--surface-color); border: 1px solid var(--border-color);">
        <h2 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('landing_cms_top_bar_section') }}</h2>
        <div class="grid gap-3 sm:grid-cols-3">
            @foreach (['phone', 'emergency', 'hours'] as $f)
                <label class="block text-sm">
                    <span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField($f) }}</span>
                    <input type="text" class="landing-json-field w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"
                           data-json-path="topBar.{{ $f }}" value="{{ $jb('topBar.'.$f) }}">
                </label>
            @endforeach
        </div>
    </div>

    <div class="admin-card rounded-xl p-6 sm:p-8 space-y-4" style="background: var(--surface-color); border: 1px solid var(--border-color);">
        <h2 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('landing_cms_hero_section') }}</h2>
        <div class="grid gap-3">
            @foreach (['tagline', 'headline', 'subhead', 'cta_primary', 'cta_primary_href', 'cta_secondary', 'cta_secondary_href'] as $f)
                <label class="block text-sm">
                    <span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField($f) }}</span>
                    <input type="text" class="landing-json-field w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"
                           data-json-path="hero.{{ $f }}" value="{{ $jb('hero.'.$f) }}">
                </label>
            @endforeach
        </div>
    </div>

    @foreach (['about' => ['title', 'lead', 'body'], 'contact' => ['title', 'body', 'cta'], 'cta' => ['title', 'body', 'button'], 'footer' => ['line']] as $sk => $fields)
        <div class="admin-card rounded-xl p-6 sm:p-8 space-y-4" style="background: var(--surface-color); border: 1px solid var(--border-color);">
            <h2 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ $cmsSection($sk) }}</h2>
            <div class="grid gap-3">
                @foreach ($fields as $field)
                    <label class="block text-sm">
                        <span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField($field) }}</span>
                        @if ($field === 'body' || $field === 'lead')
                            <textarea rows="3" class="landing-json-field w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"
                                      data-json-path="{{ $sk }}.{{ $field }}">{{ $jb($sk.'.'.$field) }}</textarea>
                        @else
                            <input type="text" class="landing-json-field w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"
                                   data-json-path="{{ $sk }}.{{ $field }}" value="{{ $jb($sk.'.'.$field) }}">
                        @endif
                    </label>
                @endforeach
            </div>
        </div>
    @endforeach

    <div class="admin-card rounded-xl p-6 sm:p-8 space-y-4" style="background: var(--surface-color); border: 1px solid var(--border-color);">
        <h2 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('landing_cms_why_us_section') }}</h2>
        <div class="grid gap-3">
            <label class="block text-sm">
                <span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField('title') }}</span>
                <input type="text" class="landing-json-field w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"
                       data-json-path="whyUs.title" value="{{ $jb('whyUs.title') }}">
            </label>
            <label class="block text-sm">
                <span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField('subtitle') }}</span>
                <input type="text" class="landing-json-field w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"
                       data-json-path="whyUs.subtitle" value="{{ $jb('whyUs.subtitle') }}">
            </label>
        </div>
    </div>

    <div class="admin-card rounded-xl p-6 sm:p-8 space-y-4" style="background: var(--surface-color); border: 1px solid var(--border-color);">
        <h2 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('landing_cms_quick_booking_section') }}</h2>
        <label class="inline-flex items-center gap-2 text-sm cursor-pointer {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
            <input type="checkbox" class="landing-json-field rounded border" data-json-path="quickBooking.enabled" @checked($jb('quickBooking.enabled'))>
            <span style="color: var(--text-color);">{{ get_translation('enabled') }}</span>
        </label>
        <div class="grid gap-3 border-t pt-4" style="border-color: var(--border-color);">
            <label class="block text-sm">
                <span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField('title') }}</span>
                <input type="text" class="landing-json-field w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"
                       data-json-path="quickBooking.copy.title" value="{{ $jb('quickBooking.copy.title') }}">
            </label>
            <label class="block text-sm">
                <span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField('hint') }}</span>
                <input type="text" class="landing-json-field w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"
                       data-json-path="quickBooking.copy.hint" value="{{ $jb('quickBooking.copy.hint') }}">
            </label>
        </div>
    </div>

    <div class="admin-card rounded-xl p-6 sm:p-8 space-y-6 overflow-x-auto" style="background: var(--surface-color); border: 1px solid var(--border-color);">
        <h2 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ get_translation('landing_cms_navigation_section') }}</h2>
        @foreach (data_get($bundle, 'nav', []) as $i => $navItem)
            @if (! is_array($navItem))
                @continue
            @endif
            <div class="border rounded-lg p-4 space-y-3" style="border-color: var(--border-color);">
                <p class="text-xs font-mono {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">{{ get_translation('landing_cms_json_nav_item') }} {{ $i + 1 }}</p>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <label class="block text-sm sm:col-span-2">
                        <span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField('href') }}</span>
                        <input type="text" class="landing-json-field w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"
                               data-json-path="nav.{{ $i }}.href" value="{{ data_get($navItem, 'href') }}">
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
                        <input type="checkbox" class="landing-json-field rounded border" data-json-path="nav.{{ $i }}.is_visible" @checked(data_get($navItem, 'is_visible', true))>
                        {{ get_translation('landing_cms_nav_visible') }}
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
                        <input type="checkbox" class="landing-json-field rounded border" data-json-path="nav.{{ $i }}.is_cta" @checked(data_get($navItem, 'is_cta'))>
                        {{ get_translation('landing_cms_nav_cta') }}
                    </label>
                </div>
                <label class="block text-sm">
                    <span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ get_translation('landing_cms_nav_label', ['locale' => $localeLabel($loc)]) }}</span>
                    <input type="text" class="landing-json-field w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"
                           data-json-path="nav.{{ $i }}.label" value="{{ data_get($navItem, 'label') }}">
                </label>
                <label class="block text-sm">
                    <span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField('slug') }} (route_key)</span>
                    <input type="text" class="landing-json-field w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"
                           data-json-path="nav.{{ $i }}.route_key" value="{{ data_get($navItem, 'route_key') }}">
                </label>
                <label class="block text-sm">
                    <span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">icon</span>
                    <input type="text" class="landing-json-field w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"
                           data-json-path="nav.{{ $i }}.icon" value="{{ data_get($navItem, 'icon') }}">
                </label>
            </div>
        @endforeach
    </div>

    @foreach ([
        'whyUs.items' => ['title' => get_translation('landing_cms_json_why_us_cards'), 'prefix' => 'whyUs.items'],
        'departments' => ['title' => $cmsBlock('departments'), 'prefix' => 'departments'],
        'featuredDoctors' => ['title' => $cmsBlock('featured_doctors'), 'prefix' => 'featuredDoctors'],
        'testimonials' => ['title' => $cmsBlock('testimonials'), 'prefix' => 'testimonials'],
    ] as $listKey => $meta)
        @php
            $rows = data_get($bundle, $listKey, []);
            if (! is_array($rows)) {
                $rows = [];
            }
        @endphp
        @if ($listKey === 'whyUs.items' || count($rows) > 0)
            <div class="admin-card rounded-xl p-6 sm:p-8 space-y-6 overflow-x-auto" style="background: var(--surface-color); border: 1px solid var(--border-color);">
                <h2 class="text-lg font-semibold {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-color);">{{ $meta['title'] }}</h2>
                @foreach ($rows as $i => $row)
                    @if (! is_array($row))
                        @continue
                    @endif
                    <div class="border rounded-lg p-4 space-y-3" style="border-color: var(--border-color);">
                        <p class="text-xs font-mono {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ data_get($row, 'type', 'row') }} #{{ $i + 1 }}</p>
                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                            <label class="block text-sm">
                                <span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">type</span>
                                <input type="text" class="landing-json-field w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"
                                       data-json-path="{{ $meta['prefix'] }}.{{ $i }}.type" value="{{ data_get($row, 'type') }}">
                            </label>
                            <label class="block text-sm">
                                <span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField('slug') }}</span>
                                <input type="text" class="landing-json-field w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"
                                       data-json-path="{{ $meta['prefix'] }}.{{ $i }}.slug" value="{{ data_get($row, 'slug') }}">
                            </label>
                            <label class="block text-sm sm:col-span-2">
                                <span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField('image_path') }} (image)</span>
                                <input type="text" class="landing-json-field w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"
                                       data-json-path="{{ $meta['prefix'] }}.{{ $i }}.image" value="{{ data_get($row, 'image') }}">
                            </label>
                            <label class="block text-sm sm:col-span-2">
                                <span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField('href') }}</span>
                                <input type="text" class="landing-json-field w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"
                                       data-json-path="{{ $meta['prefix'] }}.{{ $i }}.href" value="{{ data_get($row, 'href') }}">
                            </label>
                            <label class="block text-sm">
                                <span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">user_id</span>
                                <input type="text" class="landing-json-field w-full rounded-lg border px-3 py-2 text-sm" style="border-color: var(--border-color);"
                                       data-json-path="{{ $meta['prefix'] }}.{{ $i }}.user_id" value="{{ data_get($row, 'user_id') }}">
                            </label>
                        </div>
                        <div class="grid gap-2 border-t pt-3" style="border-color: var(--border-color);">
                            <label class="block text-sm">
                                <span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField('title') }}</span>
                                <input type="text" class="landing-json-field w-full rounded border px-2 py-1 text-sm" style="border-color: var(--border-color);"
                                       data-json-path="{{ $meta['prefix'] }}.{{ $i }}.title" value="{{ data_get($row, 'title') }}">
                            </label>
                            <label class="block text-sm">
                                <span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField('subtitle') }}</span>
                                <input type="text" class="landing-json-field w-full rounded border px-2 py-1 text-sm" style="border-color: var(--border-color);"
                                       data-json-path="{{ $meta['prefix'] }}.{{ $i }}.subtitle" value="{{ data_get($row, 'subtitle') }}">
                            </label>
                            <label class="block text-sm">
                                <span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField('body') }}</span>
                                <textarea rows="2" class="landing-json-field w-full rounded border px-2 py-1 text-sm" style="border-color: var(--border-color);"
                                          data-json-path="{{ $meta['prefix'] }}.{{ $i }}.body">{{ data_get($row, 'body') }}</textarea>
                            </label>
                            <label class="block text-sm">
                                <span class="block mb-1 opacity-80 {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">{{ $cmsField('cta_label') }}</span>
                                <input type="text" class="landing-json-field w-full rounded border px-2 py-1 text-sm" style="border-color: var(--border-color);"
                                       data-json-path="{{ $meta['prefix'] }}.{{ $i }}.cta_label" value="{{ data_get($row, 'cta_label') }}">
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endforeach
</div>

<script type="application/json" class="landing-json-initial" data-locale="{{ $loc }}">@json($bundle)</script>
