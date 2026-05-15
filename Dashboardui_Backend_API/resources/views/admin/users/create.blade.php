@extends('layouts.admin')

@section('title', get_translation('create_user'))

@section('content')
    <h1 class="text-2xl font-semibold mb-4">{{ get_translation('create_user') }}</h1>

    <form method="POST" action="{{ route_with_lang('admin.users.store') }}" class="bg-white border rounded-lg p-4 space-y-4 max-w-xl">
        @csrf
        <div>
            <label class="block text-sm mb-1">{{ get_translation('name') }}</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-md border border-gray-300 px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm mb-1">{{ get_translation('email') }}</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-md border border-gray-300 px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm mb-1">{{ get_translation('role') }}</label>
            <select name="role" class="w-full rounded-md border border-gray-300 px-3 py-2" required>
                @foreach($manageableRoles as $role)
                    <option value="{{ $role }}" @selected(old('role') === $role)>{{ ucfirst($role) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">{{ get_translation('password') }}</label>
            <input type="password" name="password" class="w-full rounded-md border border-gray-300 px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm mb-1">{{ get_translation('confirm_password') }}</label>
            <input type="password" name="password_confirmation" class="w-full rounded-md border border-gray-300 px-3 py-2" required>
        </div>
        <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-white">{{ get_translation('save') }}</button>
    </form>
@endsection
