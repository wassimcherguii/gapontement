<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Location;
use App\Models\Provider;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function create(Request $request)
    {
        $providers = Provider::query()
            ->with('user:id,name')
            ->where('is_active', true)
            ->whereHas('user', fn ($query) => $query->where('role', 'doctor'))
            ->orderBy('id')
            ->get();

        $locations = Location::query()->where('is_active', true)->orderBy('name')->get();
        $linkedPatients = $request->user()->isCompanion()
            ? $request->user()->companionPatients()->wherePivot('can_book', true)->get(['users.id', 'users.name'])
            : collect();

        return view('patient.appointments.create', [
            'providers' => $providers,
            'locations' => $locations,
            'linkedPatients' => $linkedPatients,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Appointment::class);

        $validated = $request->validate([
            'patient_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'provider_id' => ['required', 'integer', 'exists:providers,id'],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'starts_at' => ['required', 'date', 'after:now'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'patient_notes' => ['nullable', 'string'],
        ], [
            'provider_id.required' => get_translation('provider_required'),
            'provider_id.exists' => get_translation('provider_not_found'),
            'starts_at.required' => get_translation('appointment_start_required'),
            'starts_at.after' => get_translation('appointment_start_must_be_future'),
            'ends_at.required' => get_translation('appointment_end_required'),
            'ends_at.after' => get_translation('appointment_end_after_start'),
        ]);

        $patientUserId = $this->resolvePatientUserId($request, $validated['patient_user_id'] ?? null);
        if (! $patientUserId) {
            return back()->withErrors([
                'patient_user_id' => get_translation('patient_not_linked_for_companion'),
            ])->withInput();
        }

        $provider = Provider::query()
            ->with('user:id,role')
            ->where('id', $validated['provider_id'])
            ->where('is_active', true)
            ->first();
        if (! $provider || ! $provider->user || ! $provider->user->isDoctor()) {
            return back()->withErrors([
                'provider_id' => get_translation('provider_not_found'),
            ])->withInput();
        }

        if ($this->hasProviderOverlap($provider->id, (string) $validated['starts_at'], (string) $validated['ends_at'])) {
            return back()->withErrors([
                'starts_at' => get_translation('appointment_time_conflict'),
            ])->withInput();
        }

        Appointment::query()->create([
            'patient_user_id' => $patientUserId,
            'provider_id' => $provider->id,
            'location_id' => $validated['location_id'] ?? $provider->location_id,
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'status' => Appointment::STATUS_PENDING,
            'patient_notes' => $validated['patient_notes'] ?? null,
        ]);

        return redirect(route_with_lang('patient.dashboard'))
            ->with('success', get_translation('appointment_created_success'));
    }

    public function show(string $lang, Appointment $appointment)
    {
        $this->authorize('view', $appointment);

        return view('patient.appointments.show', [
            'appointment' => $appointment->load(['patient:id,name', 'provider.user:id,name', 'location:id,name']),
        ]);
    }

    public function cancel(Request $request, string $lang, Appointment $appointment)
    {
        $this->authorize('update', $appointment);

        $request->validate([
            'cancellation_reason' => ['nullable', 'string'],
        ]);

        $appointment->update([
            'status' => Appointment::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => $request->input('cancellation_reason'),
        ]);

        return redirect(route_with_lang('patient.appointments.show', ['appointment' => $appointment->id]))
            ->with('success', get_translation('appointment_cancelled_success'));
    }

    private function resolvePatientUserId(Request $request, ?int $requestedPatientId): ?int
    {
        $user = $request->user();

        if ($user->isPatient()) {
            if ($requestedPatientId !== null && $requestedPatientId !== (int) $user->id) {
                return null;
            }

            return (int) $user->id;
        }

        if ($user->isCompanion()) {
            if ($requestedPatientId === null) {
                return null;
            }

            return $user->companionPatients()
                ->where('users.id', $requestedPatientId)
                ->wherePivot('can_book', true)
                ->exists() ? $requestedPatientId : null;
        }

        return null;
    }

    private function hasProviderOverlap(int $providerId, string $startsAt, string $endsAt): bool
    {
        return Appointment::query()
            ->where('provider_id', $providerId)
            ->where('status', '!=', Appointment::STATUS_CANCELLED)
            ->where('starts_at', '<', $endsAt)
            ->where('ends_at', '>', $startsAt)
            ->exists();
    }
}
