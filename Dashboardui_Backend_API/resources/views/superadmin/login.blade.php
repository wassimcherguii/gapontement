@extends('layouts.login')

@section('title', get_translation('superadmin_login') ?? 'Super Admin Login')
@section('description', get_translation('superadmin_login_subtitle') ?? 'Please sign in to your super admin account')

@section('content')
<!-- Main Login Container -->
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Login Form -->
        @include('components.superadmin-login-form')
    </div>
</div>

<!-- Floating Settings Button -->
@include('components.floating-settings-button')

<!-- Settings Modal -->
@include('components.settings-modal')
@endsection

@push('scripts')
    @include('components.modal-scripts')
    @include('components.password-toggle-scripts')
@endpush
