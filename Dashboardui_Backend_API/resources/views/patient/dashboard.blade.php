@extends('layouts.clinic-portal')

@section('title', get_translation('patient_dashboard'))

@section('header_links')
    <a href="{{ route_with_lang('patient.appointments.create') }}" class="rounded-md bg-blue-600 px-3 py-2 text-sm text-white">
        {{ get_translation('book_appointment') }}
    </a>
    <form method="POST" action="{{ route_with_lang('patient.logout') }}">
        @csrf
        <button type="submit" class="rounded-md border border-gray-300 px-3 py-2 text-sm">{{ get_translation('logout') }}</button>
    </form>
@endsection

@section('content')
    <h1 class="text-2xl font-semibold mb-4">{{ get_translation('patient_dashboard') }}</h1>

    <h2 class="text-lg font-semibold mb-2">{{ get_translation('upcoming_appointments') }}</h2>
    <div class="bg-white rounded-lg border">
        @forelse($upcomingAppointments as $appointment)
            <a href="{{ route_with_lang('patient.appointments.show', ['appointment' => $appointment->id]) }}" class="block px-4 py-3 border-b hover:bg-gray-50">
                <div class="font-medium">{{ optional(optional($appointment->provider)->user)->name ?? '-' }}</div>
                <div class="text-sm text-gray-600">{{ $appointment->starts_at?->format('Y-m-d H:i') }} - {{ $appointment->ends_at?->format('H:i') }}</div>
                <div class="text-xs text-gray-500">{{ $appointment->status }}</div>
            </a>
        @empty
            <div class="px-4 py-3 text-sm text-gray-600">{{ get_translation('no_data') }}</div>
        @endforelse
    </div>

    <h2 class="text-lg font-semibold mt-6 mb-2">{{ get_translation('past_appointments') }}</h2>
    <div class="bg-white rounded-lg border">
        @forelse($pastAppointments as $appointment)
            <a href="{{ route_with_lang('patient.appointments.show', ['appointment' => $appointment->id]) }}" class="block px-4 py-3 border-b hover:bg-gray-50">
                <div class="font-medium">{{ optional(optional($appointment->provider)->user)->name ?? '-' }}</div>
                <div class="text-sm text-gray-600">{{ $appointment->starts_at?->format('Y-m-d H:i') }}</div>
                <div class="text-xs text-gray-500">{{ $appointment->status }}</div>
            </a>
        @empty
            <div class="px-4 py-3 text-sm text-gray-600">{{ get_translation('no_data') }}</div>
        @endforelse
    </div>
@endsection
