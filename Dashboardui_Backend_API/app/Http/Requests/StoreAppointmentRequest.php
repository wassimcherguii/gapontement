<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'patient_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'provider_id' => ['required', 'integer', 'exists:providers,id'],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'starts_at' => ['required', 'date', 'after:now'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'patient_notes' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'provider_id.required' => get_translation('provider_required'),
            'provider_id.exists' => get_translation('provider_not_found'),
            'starts_at.required' => get_translation('appointment_start_required'),
            'starts_at.after' => get_translation('appointment_start_must_be_future'),
            'ends_at.required' => get_translation('appointment_end_required'),
            'ends_at.after' => get_translation('appointment_end_after_start'),
        ];
    }
}
