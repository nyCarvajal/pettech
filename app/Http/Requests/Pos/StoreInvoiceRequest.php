<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'exists:clients,id'],
            'pet_id' => ['nullable', 'exists:pets,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
