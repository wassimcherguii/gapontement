<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppointmentRequest extends FormRequest
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
            'starts_at' => ['sometimes', 'required', 'date', 'after:now'],
            'ends_at' => ['sometimes', 'required', 'date', 'after:starts_at'],
            'status' => ['sometimes', 'required', Rule::in(Appointment::statuses())],
            'patient_notes' => ['sometimes', 'nullable', 'string'],
            'internal_notes' => ['sometimes', 'nullable', 'string'],
            'cancellation_reason' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
