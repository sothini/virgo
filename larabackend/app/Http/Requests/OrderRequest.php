<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'symbol' => ['required', 'string', 'max:10', 'uppercase'],
            'side' => ['required', 'string', Rule::in(['buy', 'sell'])],
            'price' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'amount' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'status' => ['nullable', 'integer', Rule::in([1, 2, 3])],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'symbol.required' => 'The symbol is required.',
            'symbol.max' => 'The symbol may not be greater than 10 characters.',
            'side.required' => 'The side is required.',
            'side.in' => 'The side must be either "buy" or "sell".',
            'price.required' => 'The price is required.',
            'price.numeric' => 'The price must be a number.',
            'price.min' => 'The price must be at least 0.',
            'amount.required' => 'The amount is required.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.min' => 'The amount must be at least 0.',
            'status.in' => 'The status must be 1 (open), 2 (filled), or 3 (cancelled).',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('symbol')) {
            $this->merge([
                'symbol' => strtoupper($this->symbol),
            ]);
        }
    }
}
