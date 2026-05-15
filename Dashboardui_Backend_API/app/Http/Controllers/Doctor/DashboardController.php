<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $provider = $request->user()->providerProfile;

        $todayAppointments = Appointment::query()
            ->with(['patient:id,name', 'location:id,name'])
            ->where('provider_id', optional($provider)->id)
            ->whereDate('starts_at', now()->toDateString())
            ->orderBy('starts_at')
            ->get();

        $upcomingAppointments = Appointment::query()
            ->with(['patient:id,name', 'location:id,name'])
            ->where('provider_id', optional($provider)->id)
            ->where('starts_at', '>', now())
            ->orderBy('starts_at')
            ->limit(25)
            ->get();

        return view('doctor.dashboard', [
            'todayAppointments' => $todayAppointments,
            'upcomingAppointments' => $upcomingAppointments,
        ]);
    }

    public function updateStatus(Request $request, string $lang, Appointment $appointment)
    {
        $this->authorize('update', $appointment);

        $validated = $request->validate([
            'status' => ['required', Rule::in([
                Appointment::STATUS_CONFIRMED,
                Appointment::STATUS_CANCELLED,
                Appointment::STATUS_COMPLETED,
            ])],
            'internal_notes' => ['nullable', 'string'],
            'cancellation_reason' => ['nullable', 'string'],
        ]);

        $appointment->status = $validated['status'];
        $appointment->internal_notes = $validated['internal_notes'] ?? $appointment->internal_notes;

        if ($validated['status'] === Appointment::STATUS_CANCELLED) {
            $appointment->cancelled_at = now();
            $appointment->cancellation_reason = $validated['cancellation_reason'] ?? null;
        } else {
            $appointment->cancelled_at = null;
            $appointment->cancellation_reason = null;
        }

        $appointment->save();

        return redirect(route_with_lang('doctor.dashboard'))
            ->with('success', get_translation('appointment_status_updated'));
    }
}
