@extends('dashboard')

@section('title', 'تعديل الدواء')

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 50%, #6d28d9 100%);
        border-radius: 20px;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(139, 92, 246, 0.25);
    }

    .form-card {
        background: var(--card);
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .form-header {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        padding: 1.5rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .form-body {
        padding: 2rem;
    }

    .form-label {
        font-weight: 600;
        color: var(--text);
        margin-bottom: 0.5rem;
    }

    .form-control {
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #8b5cf6;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
    }

    .form-select {
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-select:focus {
        border-color: #8b5cf6;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
    }

    .btn-save {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 2rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(139, 92, 246, 0.25);
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(139, 92, 246, 0.35);
        color: white;
    }

    .btn-cancel {
        background: linear-gradient(135deg, #6b7280, #4b5563);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 2rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-cancel:hover {
        transform: translateY(-2px);
        color: white;
    }

    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .is-invalid {
        border-color: #dc3545;
    }

    .medicine-info {
        background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        border-radius: 15px;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .status-active {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .status-inactive {
        background: linear-gradient(135deg, #6b7280, #4b5563);
        color: white;
    }

    .status-expired {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .status-low-stock {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .input-group-text {
        background: #f8f9fa;
        border: 2px solid #e5e7eb;
        border-right: none;
    }

    .input-group .form-control {
        border-left: none;
    }

    .input-group .form-control:focus {
        border-left: none;
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header text-center py-4 mb-4">
    <h1 class="mb-2">
        <svg class="me-3" width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
            <path d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z" />
        </svg>
        تعديل الدواء
    </h1>
    <p class="mb-0 opacity-90">تحديث بيانات الدواء في مخزون الصيدلية</p>
</div>

<!-- Form Card -->
<div class="form-card card">
    <div class="form-header">
        <h5 class="mb-0">
            <svg class="me-2" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M6,13C6.55,13 7,13.45 7,14A1,1 0 0,1 6,15A1,1 0 0,1 5,14C5,13.45 5.45,13 6,13M6,17C6.55,17 7,17.45 7,18A1,1 0 0,1 6,19A1,1 0 0,1 5,18C5,17.45 5.45,17 6,17M6,9A1,1 0 0,1 7,10A1,1 0 0,1 6,11A1,1 0 0,1 5,10A1,1 0 0,1 6,9M3,5A2,2 0 0,1 5,3H19A2,2 0 0,1 21,5V19A2,2 0 0,1 19,21H5A2,2 0 0,1 3,19V5M9,7V9H19V7H9M9,11V13H19V11H9M9,15V17H19V15H9Z" />
            </svg>
            تحديث بيانات الدواء
        </h5>
    </div>

    <div class="form-body">
        <!-- Medicine Info -->
        <div class="medicine-info">
            <div class="row">
                <div class="col-md-3">
                    <small class="text-muted">رقم الدواء:</small>
                    <div class="fw-bold">#{{ $medicine->id }}</div>
                </div>
                <div class="col-md-3">
                    <small class="text-muted">تاريخ الإضافة:</small>
                    <div class="fw-bold">{{ $medicine->created_at->format('Y/m/d H:i') }}</div>
                </div>
                <div class="col-md-3">
                    <small class="text-muted">آخر تحديث:</small>
                    <div class="fw-bold">{{ $medicine->updated_at->format('Y/m/d H:i') }}</div>
                </div>
                <div class="col-md-3">
                    <small class="text-muted">الحالة الحالية:</small>
                    <div>
                        <span class="status-badge {{ $medicine->getStatusBadgeClass() }}">
                            {{ $medicine->getStatusText() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('medicines.update', $medicine->id) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4">
                        <label for="name" class="form-label">
                            <svg class="me-1" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M6,13C6.55,13 7,13.45 7,14A1,1 0 0,1 6,15A1,1 0 0,1 5,14C5,13.45 5.45,13 6,13M6,17C6.55,17 7,17.45 7,18A1,1 0 0,1 6,19A1,1 0 0,1 5,18C5,17.45 5.45,17 6,17M6,9A1,1 0 0,1 7,10A1,1 0 0,1 6,11A1,1 0 0,1 5,10A1,1 0 0,1 6,9M3,5A2,2 0 0,1 5,3H19A2,2 0 0,1 21,5V19A2,2 0 0,1 19,21H5A2,2 0 0,1 3,19V5M9,7V9H19V7H9M9,11V13H19V11H9M9,15V17H19V15H9Z" />
                            </svg>
                            اسم الدواء *
                        </label>
                        <input type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            id="name"
                            name="name"
                            value="{{ old('name', $medicine->name) }}"
                            placeholder="أدخل اسم الدواء"
                            required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-4">
                        <label for="generic_name" class="form-label">
                            <svg class="me-1" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z" />
                            </svg>
                            الاسم العلمي
                        </label>
                        <input type="text"
                            class="form-control @error('generic_name') is-invalid @enderror"
                            id="generic_name"
                            name="generic_name"
                            value="{{ old('generic_name', $medicine->generic_name) }}"
                            placeholder="أدخل الاسم العلمي للدواء">
                        @error('generic_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-4">
                        <label for="manufacturer" class="form-label">
                            <svg class="me-1" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,3L2,12H5V20H19V12H22L12,3M12,8.75A2.25,2.25 0 0,1 14.25,11A2.25,2.25 0 0,1 12,13.25A2.25,2.25 0 0,1 9.75,11A2.25,2.25 0 0,1 12,8.75Z" />
                            </svg>
                            الشركة المصنعة
                        </label>
                        <input type="text"
                            class="form-control @error('manufacturer') is-invalid @enderror"
                            id="manufacturer"
                            name="manufacturer"
                            value="{{ old('manufacturer', $medicine->manufacturer) }}"
                            placeholder="أدخل اسم الشركة المصنعة">
                        @error('manufacturer')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-4">
                        <label for="price" class="form-label">
                            <svg class="me-1" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7,15H9C9,16.08 10.37,17 12,17C13.63,17 15,16.08 15,15C15,13.9 13.96,13.5 11.76,12.97C9.64,12.44 7,11.78 7,9C7,7.21 8.47,5.69 10.5,5.18V3H13.5V5.18C15.53,5.69 17,7.21 17,9H15C15,7.92 13.63,7 12,7C10.37,7 9,7.92 9,9C9,10.1 10.04,10.5 12.24,11.03C14.36,11.56 17,12.22 17,15C17,16.79 15.53,18.31 13.5,18.82V21H10.5V18.82C8.47,18.31 7,16.79 7,15Z" />
                            </svg>
                            السعر (ج.م) *
                        </label>
                        <div class="input-group">
                            <input type="number"
                                class="form-control @error('price') is-invalid @enderror"
                                id="price"
                                name="price"
                                value="{{ old('price', $medicine->price) }}"
                                placeholder="0.00"
                                step="0.01"
                                min="0"
                                required>
                            <span class="input-group-text">ج.م</span>
                        </div>
                        @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-4">
                        <label for="quantity_in_stock" class="form-label">
                            <svg class="me-1" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M12,6A6,6 0 0,1 18,12A6,6 0 0,1 12,18A6,6 0 0,1 6,12A6,6 0 0,1 12,6M12,8A4,4 0 0,0 8,12A4,4 0 0,0 12,16A4,4 0 0,0 16,12A4,4 0 0,0 12,8Z" />
                            </svg>
                            الكمية المتوفرة *
                        </label>
                        <input type="number"
                            class="form-control @error('quantity_in_stock') is-invalid @enderror"
                            id="quantity_in_stock"
                            name="quantity_in_stock"
                            value="{{ old('quantity_in_stock', $medicine->quantity_in_stock) }}"
                            placeholder="0"
                            min="0"
                            required>
                        @error('quantity_in_stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-4">
                        <label for="minimum_stock_level" class="form-label">
                            <svg class="me-1" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M13,14H11V10H13M13,18H11V16H13M1,21H23L12,2L1,21Z" />
                            </svg>
                            الحد الأدنى للمخزون *
                        </label>
                        <input type="number"
                            class="form-control @error('minimum_stock_level') is-invalid @enderror"
                            id="minimum_stock_level"
                            name="minimum_stock_level"
                            value="{{ old('minimum_stock_level', $medicine->minimum_stock_level) }}"
                            placeholder="10"
                            min="1"
                            max="1000"
                            required>
                        @error('minimum_stock_level')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-4">
                        <label for="unit" class="form-label">
                            <svg class="me-1" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19,7H22V9H19V12H17V9H14V7H17V4H19V7M17,19H2V17S2,10 9,10C16,10 17,17 17,19M9,12A3,3 0 0,1 6,9A3,3 0 0,1 9,6A3,3 0 0,1 12,9A3,3 0 0,1 9,12Z" />
                            </svg>
                            وحدة القياس *
                        </label>
                        <select class="form-select @error('unit') is-invalid @enderror"
                            id="unit"
                            name="unit"
                            required>
                            <option value="">اختر وحدة القياس</option>
                            <option value="قرص" {{ old('unit', $medicine->unit) == 'قرص' ? 'selected' : '' }}>قرص</option>
                            <option value="كبسولة" {{ old('unit', $medicine->unit) == 'كبسولة' ? 'selected' : '' }}>كبسولة</option>
                            <option value="شراب" {{ old('unit', $medicine->unit) == 'شراب' ? 'selected' : '' }}>شراب</option>
                            <option value="حقنة" {{ old('unit', $medicine->unit) == 'حقنة' ? 'selected' : '' }}>حقنة</option>
                            <option value="مرهم" {{ old('unit', $medicine->unit) == 'مرهم' ? 'selected' : '' }}>مرهم</option>
                            <option value="قطرة" {{ old('unit', $medicine->unit) == 'قطرة' ? 'selected' : '' }}>قطرة</option>
                            <option value="بخاخ" {{ old('unit', $medicine->unit) == 'بخاخ' ? 'selected' : '' }}>بخاخ</option>
                            <option value="علبة" {{ old('unit', $medicine->unit) == 'علبة' ? 'selected' : '' }}>علبة</option>
                        </select>
                        @error('unit')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-4">
                        <label for="expiry_date" class="form-label">
                            <svg class="me-1" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19,3H18V1H16V3H8V1H6V3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M19,19H5V8H19V19Z" />
                            </svg>
                            تاريخ انتهاء الصلاحية
                        </label>
                        <input type="date"
                            class="form-control @error('expiry_date') is-invalid @enderror"
                            id="expiry_date"
                            name="expiry_date"
                            value="{{ old('expiry_date', $medicine->expiry_date ? $medicine->expiry_date->format('Y-m-d') : '') }}"
                            min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                        @error('expiry_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-4">
                        <label for="batch_number" class="form-label">
                            <svg class="me-1" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M4,6H20V16H4M20,18A2,2 0 0,0 22,16V6C22,4.89 21.1,4 20,4H4C2.89,4 2,4.89 2,6V16A2,2 0 0,0 4,18H0V20H24V18H20Z" />
                            </svg>
                            رقم الدفعة
                        </label>
                        <input type="text"
                            class="form-control @error('batch_number') is-invalid @enderror"
                            id="batch_number"
                            name="batch_number"
                            value="{{ old('batch_number', $medicine->batch_number) }}"
                            placeholder="أدخل رقم الدفعة">
                        @error('batch_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-4">
                        <label for="status" class="form-label">
                            <svg class="me-1" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.5 2 2 6.5 2 12S6.5 22 12 22 22 17.5 22 12 17.5 2 12 2M10 17L5 12L6.41 10.59L10 14.17L17.59 6.58L19 8L10 17Z" />
                            </svg>
                            حالة الدواء *
                        </label>
                        <select class="form-select @error('status') is-invalid @enderror"
                            id="status"
                            name="status"
                            required>
                            <option value="">اختر حالة الدواء</option>
                            <option value="active" {{ old('status', $medicine->status) == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ old('status', $medicine->status) == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                            <option value="expired" {{ old('status', $medicine->status) == 'expired' ? 'selected' : '' }}>منتهي الصلاحية</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12">
                    <div class="mb-4">
                        <label for="description" class="form-label">
                            <svg class="me-1" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z" />
                            </svg>
                            وصف الدواء
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                            id="description"
                            name="description"
                            rows="4"
                            placeholder="أدخل وصف مفصل للدواء واستخداماته">{{ old('description', $medicine->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 justify-content-center">
                <button type="submit" class="btn-save">
                    <svg class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" />
                    </svg>
                    حفظ التغييرات
                </button>

                <a href="{{ route('medicines.index') }}" class="btn-cancel">
                    <svg class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" />
                    </svg>
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection