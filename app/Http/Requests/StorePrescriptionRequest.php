<?php

namespace App\Http\Requests;

use App\Models\Medicine;
use App\Models\Prescription;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StorePrescriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Prescription::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'doctor_name' => ['required', 'string', 'max:255'],
            'order_type' => ['nullable', 'string', 'in:outpatient,inpatient'],
            'room_bed_number' => ['nullable', 'string', 'max:100'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.medicine_id' => ['required', 'exists:medicines,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.dosage_instructions' => ['required', 'string', 'max:500'],
        ];

        if ($this->boolean('register_new_patient')) {
            $rules['new_patient_name'] = ['required', 'string', 'max:255'];
            $rules['new_patient_type'] = ['required', 'in:student,resident'];
            $rules['new_id_number'] = ['required', 'string', 'max:50', 'unique:patients,id_number'];
            $rules['new_contact_number'] = ['nullable', 'string', 'max:30'];
        } else {
            $rules['patient_id'] = ['required', 'exists:patients,id'];
        }

        return $rules;
    }

    /**
     * Get custom attribute names for validation errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'patient_id' => 'patient',
            'new_patient_name' => 'patient name',
            'new_patient_type' => 'patient classification',
            'new_id_number' => 'patient ID number',
            'doctor_name' => 'physician/doctor name',
            'items' => 'prescription line items',
            'items.*.medicine_id' => 'medicine',
            'items.*.quantity' => 'quantity',
            'items.*.dosage_instructions' => 'dosage instructions',
        ];
    }

    /**
     * Configure the validator instance with stock availability verification.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);

            if (! is_array($items) || empty($items)) {
                return;
            }

            // Aggregate total requested quantity per medicine
            $totals = [];
            foreach ($items as $index => $item) {
                if (isset($item['medicine_id'], $item['quantity']) && is_numeric($item['quantity'])) {
                    $totals[$item['medicine_id']][] = [
                        'index' => $index,
                        'qty' => (int) $item['quantity'],
                    ];
                }
            }

            foreach ($totals as $medicineId => $occurrences) {
                $totalRequested = array_sum(array_column($occurrences, 'qty'));
                $medicine = Medicine::with('stockBatches')->find($medicineId);

                if (! $medicine) {
                    continue;
                }

                $available = $medicine->available_stock;

                if ($totalRequested > $available) {
                    foreach ($occurrences as $occ) {
                        $validator->errors()->add(
                            "items.{$occ['index']}.quantity",
                            "Insufficient stock for {$medicine->name}. Requested total: {$totalRequested}, but only {$available} is available in active batches."
                        );
                    }
                }
            }
        });
    }
}
