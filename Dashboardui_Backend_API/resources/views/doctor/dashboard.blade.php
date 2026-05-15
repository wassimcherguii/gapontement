@extends('layouts.clinic-portal')

@section('title', get_translation('doctor_dashboard'))

@section('header_links')
    <form method="POST" action="{{ route_with_lang('doctor.logout') }}">
        @csrf
        <button type="submit" class="rounded-md border border-gray-300 px-3 py-2 text-sm">{{ get_translation('logout') }}</button>
    </form>
@endsection

@section('content')
    <h1 class="text-2xl font-semibold mb-4">{{ get_translation('doctor_dashboard') }}</h1>

    <h2 class="text-lg font-semibold mb-2">{{ get_translation('todays_appointments') }}</h2>
    <div class="bg-white rounded-lg border mb-6 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="px-3 py-2 text-left">{{ get_translation('patient') }}</th>
                    <th class="px-3 py-2 text-left">{{ get_translation('starts_at') }}</th>
                    <th class="px-3 py-2 text-left">{{ get_translation('status') }}</th>
                    <th class="px-3 py-2 text-left">{{ get_translation('actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($todayAppointments as $appointment)
                    <tr class="border-b">
                        <td class="px-3 py-2">{{ optional($appointment->patient)->name ?? '-' }}</td>
                        <td class="px-3 py-2">{{ $appointment->starts_at?->format('Y-m-d H:i') }}</td>
                        <td class="px-3 py-2">{{ $appointment->status }}</td>
                        <td class="px-3 py-2">
                            <form method="POST" action="{{ route_with_lang('doctor.appointments.status', ['appointment' => $appointment->id]) }}" class="flex gap-2">
                                @csrf
                                <button name="status" value="confirmed" class="px-2 py-1 rounded bg-green-600 text-white">{{ get_translation('confirm') }}</button>
                                <button name="status" value="completed" class="px-2 py-1 rounded bg-blue-600 text-white">{{ get_translation('complete') }}</button>
                                <button name="status" value="cancelled" class="px-2 py-1 rounded bg-red-600 text-white">{{ get_translation('cancel') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-3 py-3 text-gray-600">{{ get_translation('no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h2 class="text-lg font-semibold mb-2">{{ get_translation('upcoming_appointments') }}</h2>
    <div class="bg-white rounded-lg border overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="px-3 py-2 text-left">{{ get_translation('patient') }}</th>
                    <th class="px-3 py-2 text-left">{{ get_translation('starts_at') }}</th>
                    <th class="px-3 py-2 text-left">{{ get_translation('status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($upcomingAppointments as $appointment)
                    <tr class="border-b">
                        <td class="px-3 py-2">{{ optional($appointment->patient)->name ?? '-' }}</td>
                        <td class="px-3 py-2">{{ $appointment->starts_at?->format('Y-m-d H:i') }}</td>
                        <td class="px-3 py-2">{{ $appointment->status }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-3 py-3 text-gray-600">{{ get_translation('no_data') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
