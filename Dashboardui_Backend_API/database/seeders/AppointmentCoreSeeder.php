<?php

namespace Database\Seeders;

use App\Models\CompanionPatient;
use App\Models\Location;
use App\Models\PatientProfile;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Seeder;

class AppointmentCoreSeeder extends Seeder
{
    public function run(): void
    {
        $mainLocation = Location::query()->firstOrCreate(
            ['name' => 'Main hospital'],
            [
                'address' => null,
                'timezone' => 'UTC',
                'is_active' => true,
            ]
        );

        $doctorUsers = User::query()->where('role', 'doctor')->get();
        foreach ($doctorUsers as $doctorUser) {
            Provider::query()->updateOrCreate(
                ['user_id' => $doctorUser->id],
                [
                    'location_id' => $mainLocation->id,
                    'title' => 'Doctor',
                    'bio' => null,
                    'is_active' => true,
                ]
            );
        }

        $patient = User::query()->where('role', 'patient')->first();
        if ($patient) {
            PatientProfile::query()->firstOrCreate(['user_id' => $patient->id]);
        }

        $companion = User::query()->where('role', 'companion')->first();
        if ($patient && $companion) {
            CompanionPatient::query()->firstOrCreate(
                [
                    'companion_user_id' => $companion->id,
                    'patient_user_id' => $patient->id,
                ],
                [
                    'can_book' => true,
                ]
            );
        }
    }
}
