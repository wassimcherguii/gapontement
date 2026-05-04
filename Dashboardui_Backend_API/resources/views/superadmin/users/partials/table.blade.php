@if($users->count() > 0)
<div class="overflow-x-auto">
    <table class="min-w-full divide-y" style="border-color: var(--border-color);">
        <thead style="background: var(--hover-bg);">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}"
                    style="color: var(--text-secondary-color);">
                    {{ get_translation('name') ?? 'Name' }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}"
                    style="color: var(--text-secondary-color);">
                    {{ get_translation('email') ?? 'Email' }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}"
                    style="color: var(--text-secondary-color);">
                    {{ get_translation('role') ?? 'Role' }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}"
                    style="color: var(--text-secondary-color);">
                    {{ get_translation('created_at') ?? 'Created At' }}
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}"
                    style="color: var(--text-secondary-color);">
                    {{ get_translation('actions') ?? 'Actions' }}
                </th>
            </tr>
        </thead>
        <tbody class="divide-y" style="border-color: var(--border-color);">
            @foreach($users as $user)
            <tr class="superadmin-hover">
                <td class="px-6 py-4 whitespace-nowrap text-sm {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}"
                    style="color: var(--text-color);">
                    {{ $user->name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}"
                    style="color: var(--text-color);">
                    {{ $user->email }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}">
                    <span class="px-2 py-1 rounded-full text-xs font-medium" style="background: var(--primary-color)20; color: var(--primary-color);">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm {{ is_rtl_language(app()->getLocale()) ? 'text-right' : 'text-left' }}"
                    style="color: var(--text-color);">
                    <div>
                        <span>{{ $user->created_at->format('Y-m-d H:i:s') }}</span>
                        <br>
                        <span class="text-xs" style="color: var(--text-secondary-color);">{{ $user->created_at->diffForHumans() }}</span>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center space-x-2 {{ is_rtl_language(app()->getLocale()) ? 'space-x-reverse' : '' }}">
                        <a href="{{ route_with_lang('superadmin.users.edit', ['id' => $user->id]) }}" 
                           class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200">
                            {{ get_translation('edit') ?? 'Edit' }}
                        </a>
                        @if($user->id !== Auth::id())
                        <form method="POST" action="{{ route_with_lang('superadmin.users.destroy', ['id' => $user->id]) }}" 
                              class="inline" 
                              onsubmit="return confirm('{{ get_translation('confirm_delete_user') ?? 'Are you sure you want to delete this user?' }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 transition-colors duration-200">
                                {{ get_translation('delete') ?? 'Delete' }}
                            </button>
                        </form>
                        @else
                        <span class="text-gray-400 cursor-not-allowed">{{ get_translation('delete') ?? 'Delete' }}</span>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="px-6 py-12 text-center">
    <div class="flex flex-col items-center">
        {!! lucide_icon('file-text', 'w-12 h-12', 'var(--text-secondary-color)') !!}
        <p class="text-lg font-medium mt-4" style="color: var(--text-color);">{{ get_translation('no_data') ?? 'No data available' }}</p>
        <p class="text-sm mt-2" style="color: var(--text-secondary-color);">{{ get_translation('no_data_description') ?? 'There are no records to display at the moment.' }}</p>
    </div>
</div>
@endif
