@extends('layouts.login')

@section('title', get_translation('patient_login'))

@section('content')
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white rounded-xl shadow p-6">
        <h1 class="text-2xl font-semibold mb-1">{{ get_translation('patient_login') }}</h1>
        <p class="text-sm text-gray-600 mb-4">{{ get_translation('patient_login_subtitle') }}</p>

        @if ($errors->any())
            <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-3 text-red-700 text-sm">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route_with_lang('patient.login.submit') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm mb-1">{{ get_translation('email') }}</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-md border border-gray-300 px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm mb-1">{{ get_translation('password') }}</label>
                <input type="password" name="password" class="w-full rounded-md border border-gray-300 px-3 py-2" required>
            </div>
            <button type="submit" class="w-full rounded-md bg-blue-600 px-4 py-2 text-white">{{ get_translation('sign_in') }}</button>
        </form>
    </div>
</div>
@endsection
