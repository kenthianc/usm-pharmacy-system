<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DispensePrescriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('pharmacist') || $this->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'string', 'in:cash,card,insurance,hospital_bill'],
            'discount_type' => ['nullable', 'string', 'in:regular,senior,pwd,student'],
            'discount_id_number' => ['nullable', 'string', 'max:50', 'required_if:discount_type,senior,pwd'],
            'allocations' => ['required', 'array'],
            'allocations.*' => ['required', 'array'],
            'allocations.*.*' => ['required', 'integer', 'min:0'],
        ];
    }
}
