<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Provider;
use App\Models\User;
use Database\Seeders\AppointmentCoreSeeder;
use Database\Seeders\UserRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentVerticalSliceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserRoleSeeder::class);
        $this->seed(AppointmentCoreSeeder::class);
    }

    public function test_patient_can_login_and_see_dashboard(): void
    {
        $response = $this->post('/en/patient/login', [
            'email' => 'patient@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/en/patient/dashboard');

        $dashboard = $this->get('/en/patient/dashboard');
        $dashboard->assertOk();
        $dashboard->assertSee('Patient dashboard', false);
    }

    public function test_patient_can_create_appointment_via_web_form(): void
    {
        $patient = User::query()->where('email', 'patient@example.com')->firstOrFail();
        $provider = Provider::query()->firstOrFail();
        $this->actingAs($patient);

        $response = $this->post('/en/patient/appointments', [
            'provider_id' => $provider->id,
            'starts_at' => now()->addDay()->setHour(10)->setMinute(0)->toDateTimeString(),
            'ends_at' => now()->addDay()->setHour(10)->setMinute(30)->toDateTimeString(),
            'patient_notes' => 'Needs a routine checkup',
        ]);

        $response->assertRedirect('/en/patient/dashboard');
        $this->assertDatabaseHas('appointments', [
            'patient_user_id' => $patient->id,
            'provider_id' => $provider->id,
            'status' => Appointment::STATUS_PENDING,
        ]);
    }

    public function test_doctor_can_confirm_appointment_via_web(): void
    {
        $doctor = User::query()->where('email', 'doctor@example.com')->firstOrFail();
        $patient = User::query()->where('email', 'patient@example.com')->firstOrFail();
        $provider = Provider::query()->where('user_id', $doctor->id)->firstOrFail();

        $appointment = Appointment::query()->create([
            'patient_user_id' => $patient->id,
            'provider_id' => $provider->id,
            'location_id' => $provider->location_id,
            'starts_at' => now()->addDay()->setHour(9)->toDateTimeString(),
            'ends_at' => now()->addDay()->setHour(9)->setMinute(30)->toDateTimeString(),
            'status' => Appointment::STATUS_PENDING,
        ]);

        $response = $this->actingAs($doctor)->post('/en/doctor/appointments/'.$appointment->id.'/status', [
            'status' => Appointment::STATUS_CONFIRMED,
        ]);

        $response->assertRedirect('/en/doctor/dashboard');
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => Appointment::STATUS_CONFIRMED,
        ]);
    }

    public function test_api_patient_token_lists_appointments(): void
    {
        $patient = User::query()->where('email', 'patient@example.com')->firstOrFail();
        $provider = Provider::query()->firstOrFail();

        $appointment = Appointment::query()->create([
            'patient_user_id' => $patient->id,
            'provider_id' => $provider->id,
            'location_id' => $provider->location_id,
            'starts_at' => now()->addDay()->setHour(11)->toDateTimeString(),
            'ends_at' => now()->addDay()->setHour(11)->setMinute(30)->toDateTimeString(),
            'status' => Appointment::STATUS_PENDING,
        ]);

        $token = $patient->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/me/appointments');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonFragment(['id' => $appointment->id]);
    }
}
