<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EncounterRequest extends FormRequest
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
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'visit_date' => 'required|date|after_or_equal:today',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'patient_id.required' => 'اختيار المريض مطلوب',
            'patient_id.exists' => 'المريض المحدد غير موجود',

            'doctor_id.required' => 'اختيار الطبيب مطلوب',
            'doctor_id.exists' => 'الطبيب المحدد غير موجود',

            'visit_date.required' => 'تاريخ الزيارة مطلوب',
            'visit_date.date' => 'تاريخ الزيارة يجب أن يكون تاريخ صحيح',
            'visit_date.after_or_equal' => 'تاريخ الزيارة لا يمكن أن يكون في الماضي',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert date format if needed
        if ($this->visit_date) {
            $this->merge([
                'visit_date' => date('Y-m-d H:i:s', strtotime($this->visit_date)),
            ]);
        }
    }
}
