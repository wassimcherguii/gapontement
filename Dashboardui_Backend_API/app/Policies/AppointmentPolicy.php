<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['patient', 'companion', 'doctor', 'secretary', 'admin', 'superadmin']);
    }

    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->hasAdminPrivileges() || $user->isSecretary()) {
            return true;
        }

        if ($user->isDoctor()) {
            return (int) optional($appointment->provider)->user_id === (int) $user->id;
        }

        if ($user->isPatient()) {
            return (int) $appointment->patient_user_id === (int) $user->id;
        }

        if ($user->isCompanion()) {
            return $user->companionPatients()->where('users.id', $appointment->patient_user_id)->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isPatient() || $user->isCompanion();
    }

    public function update(User $user, Appointment $appointment): bool
    {
        if ($user->hasAdminPrivileges() || $user->isSecretary()) {
            return true;
        }

        if ($user->isDoctor()) {
            return (int) optional($appointment->provider)->user_id === (int) $user->id;
        }

        if ($user->isPatient()) {
            return (int) $appointment->patient_user_id === (int) $user->id;
        }

        if ($user->isCompanion()) {
            return $user->companionPatients()
                ->where('users.id', $appointment->patient_user_id)
                ->wherePivot('can_book', true)
                ->exists();
        }

        return false;
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $this->update($user, $appointment);
    }
}
