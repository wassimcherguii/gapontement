<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Traits\ApiResponse;
use App\Models\Appointment;
use App\Models\Provider;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $user = $request->user();
        $query = Appointment::query()->with([
            'patient:id,name,email',
            'provider.user:id,name,email',
            'location:id,name',
        ])->orderBy('starts_at');

        if ($user->isPatient()) {
            $query->where('patient_user_id', $user->id);
        } elseif ($user->isCompanion()) {
            $linkedPatientIds = $user->companionPatients()->pluck('users.id');
            $query->whereIn('patient_user_id', $linkedPatientIds);
        } elseif ($user->isDoctor()) {
            $query->whereHas('provider', function ($providerQuery) use ($user) {
                $providerQuery->where('user_id', $user->id);
            });
        } elseif (! $user->hasAdminPrivileges() && ! $user->isSecretary()) {
            return $this->forbidden('Role not allowed.');
        }

        return $this->success([
            'appointments' => $query->get(),
        ]);
    }

    public function store(StoreAppointmentRequest $request)
    {
        $this->authorize('create', Appointment::class);

        $user = $request->user();
        $validated = $request->validated();
        $patientUserId = $this->resolvePatientUserIdForCreate($user, $validated['patient_user_id'] ?? null);

        if (! $patientUserId) {
            return $this->forbidden('Not allowed to book for this patient.');
        }

        $provider = Provider::query()
            ->with('user:id,role')
            ->where('id', $validated['provider_id'])
            ->where('is_active', true)
            ->first();

        if (! $provider || ! $provider->user || ! $provider->user->isDoctor()) {
            return $this->error(get_translation('provider_not_found'), 422);
        }

        if ($this->hasProviderOverlap(
            $provider->id,
            (string) $validated['starts_at'],
            (string) $validated['ends_at']
        )) {
            return $this->error(get_translation('appointment_time_conflict'), 422);
        }

        $appointment = Appointment::query()->create([
            'patient_user_id' => $patientUserId,
            'provider_id' => $provider->id,
            'location_id' => $validated['location_id'] ?? $provider->location_id,
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'status' => Appointment::STATUS_PENDING,
            'patient_notes' => $validated['patient_notes'] ?? null,
        ]);

        return $this->success([
            'appointment' => $appointment->load(['patient:id,name,email', 'provider.user:id,name,email', 'location:id,name']),
        ], 'Appointment created', 201);
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        $this->authorize('update', $appointment);

        $user = $request->user();
        $validated = $request->validated();

        if ($user->isPatient() || $user->isCompanion()) {
            if (isset($validated['status']) && $validated['status'] !== Appointment::STATUS_CANCELLED) {
                return $this->forbidden('Patients can only cancel appointments.');
            }
        }

        if (isset($validated['starts_at']) || isset($validated['ends_at'])) {
            $startsAt = (string) ($validated['starts_at'] ?? $appointment->starts_at?->toDateTimeString());
            $endsAt = (string) ($validated['ends_at'] ?? $appointment->ends_at?->toDateTimeString());
            if ($this->hasProviderOverlap($appointment->provider_id, $startsAt, $endsAt, $appointment->id)) {
                return $this->error(get_translation('appointment_time_conflict'), 422);
            }
            $appointment->starts_at = $startsAt;
            $appointment->ends_at = $endsAt;
            if ($user->isPatient() || $user->isCompanion()) {
                $appointment->status = Appointment::STATUS_PENDING;
            }
        }

        if (array_key_exists('status', $validated)) {
            $appointment->status = $validated['status'];
            if ($validated['status'] === Appointment::STATUS_CANCELLED) {
                $appointment->cancelled_at = now();
                $appointment->cancellation_reason = $validated['cancellation_reason'] ?? null;
            } else {
                $appointment->cancelled_at = null;
                $appointment->cancellation_reason = null;
            }
        }

        if (array_key_exists('patient_notes', $validated)) {
            $appointment->patient_notes = $validated['patient_notes'];
        }

        if (array_key_exists('internal_notes', $validated)) {
            $appointment->internal_notes = $validated['internal_notes'];
        }

        $appointment->save();

        return $this->success([
            'appointment' => $appointment->load(['patient:id,name,email', 'provider.user:id,name,email', 'location:id,name']),
        ], 'Appointment updated');
    }

    private function resolvePatientUserIdForCreate($user, ?int $requestedPatientId): ?int
    {
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

            $allowed = $user->companionPatients()
                ->where('users.id', $requestedPatientId)
                ->wherePivot('can_book', true)
                ->exists();

            return $allowed ? $requestedPatientId : null;
        }

        return null;
    }

    private function hasProviderOverlap(int $providerId, string $startsAt, string $endsAt, ?int $exceptAppointmentId = null): bool
    {
        return Appointment::query()
            ->where('provider_id', $providerId)
            ->when($exceptAppointmentId, fn ($query) => $query->where('id', '!=', $exceptAppointmentId))
            ->where('status', '!=', Appointment::STATUS_CANCELLED)
            ->where('starts_at', '<', $endsAt)
            ->where('ends_at', '>', $startsAt)
            ->exists();
    }
}
