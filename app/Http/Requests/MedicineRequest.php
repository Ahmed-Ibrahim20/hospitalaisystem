<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicineRequest extends FormRequest
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
            'generic_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'manufacturer' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0|max:999999.99',
            'quantity_in_stock' => 'required|integer|min:0',
            'minimum_stock_level' => 'required|integer|min:1|max:1000',
            'unit' => 'required|string|max:50',
            'expiry_date' => 'nullable|date|after:today',
            'batch_number' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive,expired',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم الدواء مطلوب',
            'name.string' => 'اسم الدواء يجب أن يكون نص',
            'name.max' => 'اسم الدواء لا يجب أن يزيد عن 255 حرف',

            'generic_name.string' => 'الاسم العلمي يجب أن يكون نص',
            'generic_name.max' => 'الاسم العلمي لا يجب أن يزيد عن 255 حرف',

            'description.string' => 'الوصف يجب أن يكون نص',
            'description.max' => 'الوصف لا يجب أن يزيد عن 1000 حرف',

            'manufacturer.string' => 'الشركة المصنعة يجب أن تكون نص',
            'manufacturer.max' => 'الشركة المصنعة لا يجب أن تزيد عن 255 حرف',

            'price.required' => 'سعر الدواء مطلوب',
            'price.numeric' => 'سعر الدواء يجب أن يكون رقم',
            'price.min' => 'سعر الدواء لا يمكن أن يكون أقل من صفر',
            'price.max' => 'سعر الدواء لا يمكن أن يزيد عن 999999.99',

            'quantity_in_stock.required' => 'الكمية في المخزن مطلوبة',
            'quantity_in_stock.integer' => 'الكمية في المخزن يجب أن تكون رقم صحيح',
            'quantity_in_stock.min' => 'الكمية في المخزن لا يمكن أن تكون أقل من صفر',

            'minimum_stock_level.required' => 'الحد الأدنى للمخزون مطلوب',
            'minimum_stock_level.integer' => 'الحد الأدنى للمخزون يجب أن يكون رقم صحيح',
            'minimum_stock_level.min' => 'الحد الأدنى للمخزون يجب أن يكون على الأقل 1',
            'minimum_stock_level.max' => 'الحد الأدنى للمخزون لا يجب أن يزيد عن 1000',

            'unit.required' => 'وحدة القياس مطلوبة',
            'unit.string' => 'وحدة القياس يجب أن تكون نص',
            'unit.max' => 'وحدة القياس لا يجب أن تزيد عن 50 حرف',

            'expiry_date.date' => 'تاريخ انتهاء الصلاحية يجب أن يكون تاريخ صحيح',
            'expiry_date.after' => 'تاريخ انتهاء الصلاحية يجب أن يكون في المستقبل',

            'batch_number.string' => 'رقم الدفعة يجب أن يكون نص',
            'batch_number.max' => 'رقم الدفعة لا يجب أن يزيد عن 100 حرف',

            'status.required' => 'حالة الدواء مطلوبة',
            'status.in' => 'حالة الدواء يجب أن تكون نشط، غير نشط، أو منتهي الصلاحية',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->name),
            'generic_name' => trim($this->generic_name),
            'manufacturer' => trim($this->manufacturer),
            'unit' => trim($this->unit),
            'batch_number' => trim($this->batch_number),
        ]);
    }
}
