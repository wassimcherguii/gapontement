@extends('layouts.clinic-portal')

@section('title', get_translation('appointment_details'))

@section('header_links')
    <a href="{{ route_with_lang('patient.dashboard') }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm">{{ get_translation('dashboard') }}</a>
    <form method="POST" action="{{ route_with_lang('patient.logout') }}">
        @csrf
        <button type="submit" class="rounded-md border border-gray-300 px-3 py-2 text-sm">{{ get_translation('logout') }}</button>
    </form>
@endsection

@section('content')
    <h1 class="text-2xl font-semibold mb-4">{{ get_translation('appointment_details') }}</h1>

    <div class="bg-white rounded-lg border p-4 space-y-2 mb-4">
        <div><strong>{{ get_translation('doctor') }}:</strong> {{ optional(optional($appointment->provider)->user)->name ?? '-' }}</div>
        <div><strong>{{ get_translation('patient') }}:</strong> {{ optional($appointment->patient)->name ?? '-' }}</div>
        <div><strong>{{ get_translation('location') }}:</strong> {{ optional($appointment->location)->name ?? '-' }}</div>
        <div><strong>{{ get_translation('starts_at') }}:</strong> {{ $appointment->starts_at?->format('Y-m-d H:i') }}</div>
        <div><strong>{{ get_translation('ends_at') }}:</strong> {{ $appointment->ends_at?->format('Y-m-d H:i') }}</div>
        <div><strong>{{ get_translation('status') }}:</strong> {{ $appointment->status }}</div>
        <div><strong>{{ get_translation('patient_notes') }}:</strong> {{ $appointment->patient_notes ?: '-' }}</div>
    </div>

    @if($appointment->status !== \App\Models\Appointment::STATUS_CANCELLED)
        <form method="POST" action="{{ route_with_lang('patient.appointments.cancel', ['appointment' => $appointment->id]) }}" class="bg-white rounded-lg border p-4 space-y-3">
            @csrf
            <div>
                <label class="block text-sm mb-1">{{ get_translation('cancellation_reason') }}</label>
                <textarea name="cancellation_reason" rows="3" class="w-full rounded-md border border-gray-300 px-3 py-2"></textarea>
            </div>
            <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-white">{{ get_translation('cancel_appointment') }}</button>
        </form>
    @endif
@endsection
