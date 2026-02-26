<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $appointment = $this->route('appointment');

        return $appointment ? ($this->user()?->can('update', $appointment) ?? false) : false;
    }

    public function rules(): array
    {
        return [
            'code' => ['nullable', 'string', 'max:50'],
            'customer_id' => ['nullable', 'exists:clients,id'],
            'pet_id' => ['nullable', 'exists:pets,id'],
            'service_type' => ['required', 'in:'.implode(',', Appointment::SERVICE_TYPES)],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'assigned_to_user_id' => ['nullable', 'exists:users,id'],
            'status' => ['required', 'in:'.implode(',', Appointment::STATUSES)],
            'notes' => ['nullable', 'string'],
        ];
    }
}
