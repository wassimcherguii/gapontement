@props([
    'pagination' => null,
    'containerId' => 'table-container',
    'paginationContainerId' => 'pagination-container',
    'loadingId' => 'pagination-loading',
    'baseRoute' => null,
    'preserveParams' => ['tab', 'search']
])

@if($pagination && $pagination->hasPages())
<div class="mt-6 flex items-center justify-between {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}">
    <div class="text-sm {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}" style="color: var(--text-secondary-color);">
        {{ get_translation('showing') ?? 'Showing' }} 
        <span class="font-medium" style="color: var(--text-color);">{{ $pagination->firstItem() ?? 0 }}</span>
        {{ get_translation('to') ?? 'to' }}
        <span class="font-medium" style="color: var(--text-color);">{{ $pagination->lastItem() ?? 0 }}</span>
        {{ get_translation('of') ?? 'of' }}
        <span class="font-medium" style="color: var(--text-color);">{{ $pagination->total() }}</span>
        {{ get_translation('results') ?? 'results' }}
    </div>
    
    <div class="flex items-center space-x-2 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
        @if($pagination->onFirstPage())
            <span class="px-3 py-2 rounded-lg text-sm border opacity-50 cursor-not-allowed" style="border-color: var(--border-color); color: var(--text-secondary-color);">
                {{ get_translation('previous') ?? 'Previous' }}
            </span>
        @else
            <a href="{{ $pagination->previousPageUrl() }}" 
               data-page="{{ $pagination->currentPage() - 1 }}"
               class="ajax-pagination-link px-3 py-2 rounded-lg text-sm border transition-colors duration-200 superadmin-hover"
               style="border-color: var(--border-color); color: var(--text-color);">
                {{ get_translation('previous') ?? 'Previous' }}
            </a>
        @endif
        
        @foreach($pagination->getUrlRange(max(1, $pagination->currentPage() - 2), min($pagination->lastPage(), $pagination->currentPage() + 2)) as $page => $url)
            @if($page == $pagination->currentPage())
                <span class="px-3 py-2 rounded-lg text-sm font-medium text-white" style="background: var(--primary-color);">
                    {{ $page }}
                </span>
            @else
                <a href="{{ $url }}" 
                   data-page="{{ $page }}"
                   class="ajax-pagination-link px-3 py-2 rounded-lg text-sm border transition-colors duration-200 superadmin-hover"
                   style="border-color: var(--border-color); color: var(--text-color);">
                    {{ $page }}
                </a>
            @endif
        @endforeach
        
        @if($pagination->hasMorePages())
            <a href="{{ $pagination->nextPageUrl() }}" 
               data-page="{{ $pagination->currentPage() + 1 }}"
               class="ajax-pagination-link px-3 py-2 rounded-lg text-sm border transition-colors duration-200 superadmin-hover"
               style="border-color: var(--border-color); color: var(--text-color);">
                {{ get_translation('next') ?? 'Next' }}
            </a>
        @else
            <span class="px-3 py-2 rounded-lg text-sm border opacity-50 cursor-not-allowed" style="border-color: var(--border-color); color: var(--text-secondary-color);">
                {{ get_translation('next') ?? 'Next' }}
            </span>
        @endif
    </div>
</div>
@endif
