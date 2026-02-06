<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['nullable', 'string', 'max:50'],
            'customer_id' => ['required', 'exists:clients,id'],
            'pet_id' => ['required', 'exists:pets,id'],
            'service_type' => ['required', 'in:'.implode(',', Appointment::SERVICE_TYPES)],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'assigned_to_user_id' => ['nullable', 'exists:users,id'],
            'status' => ['nullable', 'in:'.implode(',', Appointment::STATUSES)],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (! $this->filled('assigned_to_user_id') || ! $this->filled('start_at') || ! $this->filled('end_at')) {
                return;
            }

            $query = Appointment::query()
                ->where('assigned_to_user_id', $this->integer('assigned_to_user_id'))
                ->where('start_at', '<', $this->input('end_at'))
                ->where('end_at', '>', $this->input('start_at'));

            if ($query->exists()) {
                $validator->errors()->add('assigned_to_user_id', 'El peluquero ya tiene una cita en ese horario.');
            }
        });
    }
}
