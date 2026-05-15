@extends('layouts.admin')

@section('title', get_translation('users_management'))

@section('content')
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">{{ get_translation('users_management') }}</h1>
        <a href="{{ route_with_lang('admin.users.create') }}" class="rounded-md bg-blue-600 px-3 py-2 text-sm text-white">
            {{ get_translation('create_user') }}
        </a>
    </div>

    <form method="GET" class="bg-white border rounded-lg p-4 mb-4 grid grid-cols-1 md:grid-cols-3 gap-3">
        <input type="text" name="search" value="{{ $search }}" placeholder="{{ get_translation('search') }}" class="rounded-md border border-gray-300 px-3 py-2">
        <select name="role" class="rounded-md border border-gray-300 px-3 py-2">
            <option value="">{{ get_translation('all_roles') }}</option>
            @foreach($manageableRoles as $role)
                <option value="{{ $role }}" @selected($activeRole === $role)>{{ ucfirst($role) }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-md border border-gray-300 px-3 py-2">{{ get_translation('filter') }}</button>
    </form>

    <div class="bg-white border rounded-lg overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="px-3 py-2 text-left">{{ get_translation('name') }}</th>
                    <th class="px-3 py-2 text-left">{{ get_translation('email') }}</th>
                    <th class="px-3 py-2 text-left">{{ get_translation('role') }}</th>
                    <th class="px-3 py-2 text-left">{{ get_translation('actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="border-b">
                        <td class="px-3 py-2">{{ $user->name }}</td>
                        <td class="px-3 py-2">{{ $user->email }}</td>
                        <td class="px-3 py-2">{{ ucfirst($user->role) }}</td>
                        <td class="px-3 py-2">
                            <a href="{{ route_with_lang('admin.users.edit', ['user' => $user->id]) }}" class="text-blue-700">{{ get_translation('edit') }}</a>
                            <form method="POST" action="{{ route_with_lang('admin.users.destroy', ['user' => $user->id]) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-700 ml-2">{{ get_translation('delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-3 py-3 text-gray-600">{{ get_translation('no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
@endsection
