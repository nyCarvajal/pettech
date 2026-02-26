<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePatientPetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'species' => ['required', 'string', 'max:255'],
            'breed' => ['nullable', 'string', 'max:255'],
            'sex' => ['required', Rule::in(['male', 'female', 'unknown'])],
            'birthdate' => ['nullable', 'date'],
            'color' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'tutor_links' => ['array'],
            'tutor_links.*.client_id' => ['required', 'integer', 'exists:clients,id'],
            'tutor_links.*.relationship' => ['required', Rule::in(['owner', 'other'])],
            'tutor_links.*.is_primary' => ['nullable', 'boolean'],
        ];
    }
}
