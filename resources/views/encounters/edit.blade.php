@extends('dashboard')

@section('title', 'تعديل الزيارة الطبية')

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #b45309 100%);
        border-radius: 20px;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(245, 158, 11, 0.25);
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
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }

    .form-select {
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-select:focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }

    .btn-save {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 2rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(245, 158, 11, 0.25);
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(245, 158, 11, 0.35);
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

    .encounter-info {
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

    .status-upcoming {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
        color: white;
    }

    .status-completed {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
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
        تعديل الزيارة الطبية
    </h1>
    <p class="mb-0 opacity-90">تحديث بيانات الزيارة الطبية</p>
</div>

<!-- Form Card -->
<div class="form-card card">
    <div class="form-header">
        <h5 class="mb-0">
            <svg class="me-2" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19.5 3.5L18.1 4.9C19.2 6 19.8 7.5 19.8 9.1C19.8 12.4 17.1 15.1 13.8 15.1S7.8 12.4 7.8 9.1C7.8 7.5 8.4 6 9.5 4.9L8.1 3.5C6.7 4.9 5.8 6.9 5.8 9.1C5.8 13.5 9.4 17.1 13.8 17.1S21.8 13.5 21.8 9.1C21.8 6.9 20.9 4.9 19.5 3.5M7 17.1C6.4 17.1 6 17.5 6 18.1V21C6 21.6 6.4 22 7 22S8 21.6 8 21V18.1C8 17.5 7.6 17.1 7 17.1Z" />
            </svg>
            تحديث بيانات الزيارة الطبية
        </h5>
    </div>

    <div class="form-body">
        <!-- Encounter Info -->
        <div class="encounter-info">
            <div class="row">
                <div class="col-md-3">
                    <small class="text-muted">رقم الزيارة:</small>
                    <div class="fw-bold">#{{ $encounter->id }}</div>
                </div>
                <div class="col-md-3">
                    <small class="text-muted">تاريخ الإنشاء:</small>
                    <div class="fw-bold">{{ $encounter->created_at->format('Y/m/d H:i') }}</div>
                </div>
                <div class="col-md-3">
                    <small class="text-muted">آخر تحديث:</small>
                    <div class="fw-bold">{{ $encounter->updated_at->format('Y/m/d H:i') }}</div>
                </div>
                <div class="col-md-3">
                    <small class="text-muted">الحالة:</small>
                    <div>
                        @php
                        $visitDate = \Carbon\Carbon::parse($encounter->visit_date);
                        $now = \Carbon\Carbon::now();

                        if ($visitDate->isFuture()) {
                        $statusText = 'قادمة';
                        $statusClass = 'status-upcoming';
                        } else {
                        $statusText = 'مكتملة';
                        $statusClass = 'status-completed';
                        }
                        @endphp
                        <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('encounters.update', $encounter->id) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4">
                        <label for="patient_id" class="form-label">
                            <svg class="me-1" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 5.5V7.5L12 8.5L9 7.5V5.5L3 7V9L9 10.5V12L3 13.5V15.5L9 14L12 15L15 14L21 15.5V13.5L15 12V10.5L21 9ZM12 10.5C10.9 10.5 10 11.4 10 12.5S10.9 14.5 12 14.5 14 13.6 14 12.5 13.1 10.5 12 10.5Z" />
                            </svg>
                            المريض *
                        </label>
                        <select class="form-select @error('patient_id') is-invalid @enderror"
                            id="patient_id"
                            name="patient_id"
                            required>
                            <option value="">اختر المريض</option>
                            @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id', $encounter->patient_id) == $patient->id ? 'selected' : '' }}>
                                {{ $patient->name }} - {{ $patient->age }} سنة ({{ $patient->gender == 'male' ? 'ذكر' : 'أنثى' }})
                            </option>
                            @endforeach
                        </select>
                        @error('patient_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-4">
                        <label for="doctor_id" class="form-label">
                            <svg class="me-1" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,2A3,3 0 0,1 15,5V11A3,3 0 0,1 12,14A3,3 0 0,1 9,11V5A3,3 0 0,1 12,2M19,18H5V16C5,13.79 7.58,12 12,12C16.42,12 19,13.79 19,16V18Z" />
                            </svg>
                            الطبيب المعالج *
                        </label>
                        <select class="form-select @error('doctor_id') is-invalid @enderror"
                            id="doctor_id"
                            name="doctor_id"
                            required>
                            <option value="">اختر الطبيب</option>
                            @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ old('doctor_id', $encounter->doctor_id) == $doctor->id ? 'selected' : '' }}>
                                د. {{ $doctor->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('doctor_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-4">
                        <label for="visit_date" class="form-label">
                            <svg class="me-1" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19,3H18V1H16V3H8V1H6V3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M19,19H5V8H19V19Z" />
                            </svg>
                            تاريخ الزيارة *
                        </label>
                        <input type="date"
                            class="form-control @error('visit_date') is-invalid @enderror"
                            id="visit_date"
                            name="visit_date"
                            value="{{ old('visit_date', \Carbon\Carbon::parse($encounter->visit_date)->format('Y-m-d')) }}"
                            min="{{ date('Y-m-d') }}"
                            required>
                        @error('visit_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-4">
                        <label for="visit_time" class="form-label">
                            <svg class="me-1" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M16.2,16.2L11,13V7H12.5V12.2L17,14.7L16.2,16.2Z" />
                            </svg>
                            وقت الزيارة *
                        </label>
                        <input type="time"
                            class="form-control @error('visit_time') is-invalid @enderror"
                            id="visit_time"
                            name="visit_time"
                            value="{{ old('visit_time', \Carbon\Carbon::parse($encounter->visit_date)->format('H:i')) }}"
                            required>
                        @error('visit_time')
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

                <a href="{{ route('encounters.index') }}" class="btn-cancel">
                    <svg class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" />
                    </svg>
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Combine date and time into visit_date field before form submission
    document.querySelector('form').addEventListener('submit', function(e) {
        const dateInput = document.getElementById('visit_date');
        const timeInput = document.getElementById('visit_time');

        if (dateInput.value && timeInput.value) {
            const combinedDateTime = dateInput.value + ' ' + timeInput.value + ':00';
            dateInput.value = combinedDateTime;
        }
    });
</script>
@endpush
@endsection