@props([
    'headers' => [],
    'data' => [],
    'actions' => true,
    'searchable' => true,
    'sortable' => true,
    'pagination' => true
])

<div class="superadmin-card rounded-xl overflow-hidden">
    @if($searchable)
    <!-- Search and Filters -->
    <div class="p-6 border-b" style="border-color: var(--border-color);">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0 {{ is_rtl_language(app()->getLocale()) ? 'md:flex-row-reverse' : '' }}">
            <div class="flex-1 max-w-md">
                <div class="relative">
                    <div class="absolute inset-y-0 {{ is_rtl_language(app()->getLocale()) ? 'right-0 pr-3' : 'left-0 pl-3' }} flex items-center pointer-events-none">
                        <svg class="h-5 w-5" style="color: var(--text-secondary-color);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" 
                           placeholder="{{ get_translation('search') }}..." 
                           class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200 {{ is_rtl_language(app()->getLocale()) ? 'text-right pr-10 pl-4' : 'text-left' }}"
                           style="background: var(--surface-color); border-color: var(--border-color); color: var(--text-color);"
                           onfocus="this.style.borderColor='var(--primary-color)'; this.style.boxShadow='0 0 0 2px var(--primary-color)20';"
                           onblur="this.style.borderColor='var(--border-color)'; this.style.boxShadow='none';">
                </div>
            </div>
            
            <div class="flex items-center space-x-3 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
                <button class="px-4 py-2 text-sm font-medium rounded-lg border transition-colors duration-200 superadmin-hover"
                        style="border-color: var(--border-color); color: var(--text-color);">
                    {{ get_translation('filter') }}
                </button>
                <button class="px-4 py-2 text-sm font-medium rounded-lg text-white transition-colors duration-200"
                        style="background: var(--primary-color);"
                        onmouseover="this.style.background='var(--primary-hover)';"
                        onmouseout="this.style.background='var(--primary-color)';">
                    {{ get_translation('export') }}
                </button>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y" style="border-color: var(--border-color);">
            <thead style="background: var(--hover-bg);">
                <tr>
                    @foreach($headers as $header)
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}"
                        style="color: var(--text-secondary-color);">
                        {{ $header }}
                    </th>
                    @endforeach
                    @if($actions)
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}"
                        style="color: var(--text-secondary-color);">
                        {{ get_translation('actions') }}
                    </th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: var(--border-color);">
                @forelse($data as $index => $row)
                <tr class="superadmin-hover">
                    @foreach($row as $key => $value)
                    <td class="px-6 py-4 text-sm {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}"
                        style="color: var(--text-color);">
                        {!! $value !!}
                    </td>
                    @endforeach
                    @if($actions)
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-2 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
                            <button class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200">
                                {{ get_translation('edit') ?? 'Edit' }}
                            </button>
                            <button class="text-red-600 hover:text-red-900 transition-colors duration-200">
                                {{ get_translation('delete') ?? 'Delete' }}
                            </button>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ count($headers) + ($actions ? 1 : 0) }}" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            {!! lucide_icon('file-text', 'w-12 h-12', 'var(--text-secondary-color)') !!}
                            <p class="text-lg font-medium" style="color: var(--text-color);">{{ get_translation('no_data') }}</p>
                            <p class="text-sm" style="color: var(--text-secondary-color);">{{ get_translation('no_data_description') }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($pagination && count($data) > 0)
    <!-- Pagination -->
    <div class="px-6 py-4 border-t flex items-center justify-between {{ is_rtl_language(app()->getLocale()) ? 'flex-row-reverse' : '' }}"
         style="border-color: var(--border-color);">
        <div class="flex-1 flex justify-between sm:hidden">
            <button class="relative inline-flex items-center px-4 py-2 border text-sm font-medium rounded-md transition-colors duration-200 superadmin-hover"
                    style="border-color: var(--border-color); color: var(--text-color);">
                {{ get_translation('previous') }}
            </button>
            <button class="ml-3 relative inline-flex items-center px-4 py-2 border text-sm font-medium rounded-md transition-colors duration-200 superadmin-hover"
                    style="border-color: var(--border-color); color: var(--text-color);">
                {{ get_translation('next') }}
            </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm" style="color: var(--text-color);">
                    {{ get_translation('showing') }} <span class="font-medium">1</span> {{ get_translation('to') }} <span class="font-medium">10</span> {{ get_translation('of') }} <span class="font-medium">97</span> {{ get_translation('results') }}
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <button class="relative inline-flex items-center px-2 py-2 rounded-l-md border text-sm font-medium transition-colors duration-200 superadmin-hover"
                            style="border-color: var(--border-color); color: var(--text-color);">
                        <span class="sr-only">{{ get_translation('previous') }}</span>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <button class="relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors duration-200"
                            style="background: var(--primary-color); border-color: var(--primary-color); color: white;">
                        1
                    </button>
                    <button class="relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors duration-200 superadmin-hover"
                            style="border-color: var(--border-color); color: var(--text-color);">
                        2
                    </button>
                    <button class="relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors duration-200 superadmin-hover"
                            style="border-color: var(--border-color); color: var(--text-color);">
                        3
                    </button>
                    <button class="relative inline-flex items-center px-2 py-2 rounded-r-md border text-sm font-medium transition-colors duration-200 superadmin-hover"
                            style="border-color: var(--border-color); color: var(--text-color);">
                        <span class="sr-only">{{ get_translation('next') }}</span>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </nav>
            </div>
        </div>
    </div>
    @endif
</div>
