<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $patientIds = $user->isCompanion()
            ? $user->companionPatients()->pluck('users.id')->all()
            : [$user->id];

        $baseQuery = Appointment::query()
            ->with(['patient:id,name', 'provider.user:id,name', 'location:id,name'])
            ->whereIn('patient_user_id', $patientIds);

        $upcomingAppointments = (clone $baseQuery)->upcoming()->get();
        $pastAppointments = (clone $baseQuery)->past()->limit(15)->get();

        return view('patient.dashboard', [
            'upcomingAppointments' => $upcomingAppointments,
            'pastAppointments' => $pastAppointments,
        ]);
    }
}
