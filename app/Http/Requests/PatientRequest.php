<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatientRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:1|max:150',
            'gender' => 'required|in:male,female',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم المريض مطلوب',
            'name.string' => 'اسم المريض يجب أن يكون نص',
            'name.max' => 'اسم المريض لا يجب أن يزيد عن 255 حرف',

            'age.required' => 'عمر المريض مطلوب',
            'age.integer' => 'عمر المريض يجب أن يكون رقم صحيح',
            'age.min' => 'عمر المريض يجب أن يكون على الأقل سنة واحدة',
            'age.max' => 'عمر المريض لا يجب أن يزيد عن 150 سنة',

            'gender.required' => 'جنس المريض مطلوب',
            'gender.in' => 'جنس المريض يجب أن يكون ذكر أو أنثى',

            'phone.string' => 'رقم الهاتف يجب أن يكون نص',
            'phone.max' => 'رقم الهاتف لا يجب أن يزيد عن 20 رقم',

            'address.string' => 'العنوان يجب أن يكون نص',
            'address.max' => 'العنوان لا يجب أن يزيد عن 500 حرف',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->name),
            'phone' => trim($this->phone),
            'address' => trim($this->address),
        ]);
    }
}
