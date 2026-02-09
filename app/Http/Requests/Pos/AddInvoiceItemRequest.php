<?php

namespace App\Http\Requests\Pos;

use Illuminate\Foundation\Http\FormRequest;

class AddInvoiceItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['nullable', 'exists:products,id'],
            'description' => ['required_without:product_id', 'string', 'max:255'],
            'qty' => ['required', 'numeric', 'min:0.01'],
            'unit_price' => ['required_without:product_id', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0'],
            'is_service' => ['nullable', 'boolean'],
        ];
    }
}
