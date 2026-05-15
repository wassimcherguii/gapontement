@extends('layouts.login')

@section('title', get_translation('login'))

@section('content')
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white rounded-xl shadow p-6 space-y-4">
        <h1 class="text-2xl font-semibold text-gray-900">{{ get_translation('choose_login_area') }}</h1>
        <p class="text-sm text-gray-600">{{ get_translation('choose_login_area_hint') }}</p>

        <a href="{{ route_with_lang('patient.login') }}" class="block w-full text-center rounded-md bg-blue-600 px-4 py-2 text-white">
            {{ get_translation('patient_login') }}
        </a>

        <a href="{{ route_with_lang('admin.login') }}" class="block w-full text-center rounded-md border border-gray-300 px-4 py-2 text-gray-700">
            {{ get_translation('staff_admin_login') }}
        </a>

        <a href="{{ route_with_lang('doctor.login') }}" class="block w-full text-center rounded-md border border-gray-300 px-4 py-2 text-gray-700">
            {{ get_translation('doctor_login') }}
        </a>
    </div>
</div>
@endsection
