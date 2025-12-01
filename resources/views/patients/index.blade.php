@extends('dashboard')

@section('title', 'إدارة المرضى')

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
        border-radius: 20px;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(16, 185, 129, 0.25);
    }

    .search-card {
        background: var(--card);
        border-radius: 15px;
        border: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
    }

    .patients-table-card {
        background: var(--card);
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .table-modern {
        margin-bottom: 0;
    }

    .table-modern thead th {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border: none;
        font-weight: 600;
        color: var(--text);
        padding: 1rem;
        text-align: center;
    }

    .table-modern tbody td {
        border: none;
        padding: 1rem;
        vertical-align: middle;
        text-align: center;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .table-modern tbody tr:hover {
        background: rgba(16, 185, 129, 0.05);
    }

    .patient-badge {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.85rem;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .gender-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .gender-male {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
    }

    .gender-female {
        background: linear-gradient(135deg, #ec4899, #db2777);
        color: white;
    }

    .age-badge {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
    }

    .btn-action {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: all 0.3s ease;
    }

    .btn-edit {
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
        color: white;
    }

    .btn-edit:hover {
        transform: scale(1.1);
        box-shadow: 0 5px 15px rgba(14, 165, 233, 0.4);
    }

    .btn-delete {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .btn-delete:hover {
        transform: scale(1.1);
        box-shadow: 0 5px 15px rgba(239, 68, 68, 0.4);
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--muted);
    }

    .empty-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .add-patient-btn {
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 2rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(16, 185, 129, 0.25);
    }

    .add-patient-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.35);
        color: white;
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header text-center py-4 mb-4">
    <h1 class="mb-2">
        <svg class="me-3" width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 5.5V7.5L12 8.5L9 7.5V5.5L3 7V9L9 10.5V12L3 13.5V15.5L9 14L12 15L15 14L21 15.5V13.5L15 12V10.5L21 9ZM12 10.5C10.9 10.5 10 11.4 10 12.5S10.9 14.5 12 14.5 14 13.6 14 12.5 13.1 10.5 12 10.5Z" />
        </svg>
        إدارة المرضى
    </h1>
    <p class="mb-3 opacity-90">إدارة وتنظيم جميع بيانات المرضى في النظام</p>
    <a href="{{ route('patients.create') }}" class="add-patient-btn">
        <svg class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z" />
        </svg>
        إضافة مريض جديد
    </a>
</div>

<!-- Search & Filters -->
<div class="search-card card">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('patients.index') }}" class="row g-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">
                        <svg class="text-primary" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z" />
                        </svg>
                    </span>
                    <input type="text" name="search" class="form-control form-control-lg"
                        placeholder="ابحث عن المرضى..."
                        value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="perPage" class="form-select form-select-lg" onchange="this.form.submit()">
                    <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10 عناصر</option>
                    <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25 عنصر</option>
                    <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50 عنصر</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <svg class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6,13H18V11H6M3,6V8H21V6M10,18H14V16H10V18Z" />
                    </svg>تطبيق الفلتر
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Patients Table -->
<div class="patients-table-card card">
    <div class="table-responsive">
        <table class="table table-modern">
            <thead>
                <tr>
                    <th width="8%">#</th>
                    <th width="25%">اسم المريض</th>
                    <th width="10%">العمر</th>
                    <th width="10%">الجنس</th>
                    <th width="15%">رقم الهاتف</th>
                    <th width="17%">تاريخ التسجيل</th>
                    <th width="15%">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $patient)
                <tr>
                    <td>
                        <div class="fw-bold text-primary">#{{ $patient->id }}</div>
                    </td>
                    <td>
                        <span class="patient-badge">
                            <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 5.5V7.5L12 8.5L9 7.5V5.5L3 7V9L9 10.5V12L3 13.5V15.5L9 14L12 15L15 14L21 15.5V13.5L15 12V10.5L21 9ZM12 10.5C10.9 10.5 10 11.4 10 12.5S10.9 14.5 12 14.5 14 13.6 14 12.5 13.1 10.5 12 10.5Z" />
                            </svg>
                            {{ $patient->name }}
                        </span>
                    </td>
                    <td>
                        <span class="age-badge">
                            {{ $patient->age }} سنة
                        </span>
                    </td>
                    <td>
                        <span class="gender-badge {{ $patient->gender == 'male' ? 'gender-male' : 'gender-female' }}">
                            <svg class="me-1" width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                @if($patient->gender == 'male')
                                <path d="M9,9C10.29,9 11.5,9.41 12.47,10.11L17.58,5H13V3H21V11H19V6.41L13.89,11.5C14.59,12.5 15,13.7 15,15A6,6 0 0,1 9,21A6,6 0 0,1 3,15A6,6 0 0,1 9,9M9,11A4,4 0 0,0 5,15A4,4 0 0,0 9,19A4,4 0 0,0 13,15A4,4 0 0,0 9,11Z" />
                                @else
                                <path d="M12,4A6,6 0 0,1 18,10A6,6 0 0,1 12,16A6,6 0 0,1 6,10A6,6 0 0,1 12,4M12,6A4,4 0 0,0 8,10A4,4 0 0,0 12,14A4,4 0 0,0 16,10A4,4 0 0,0 12,6M12,17L15.5,22H8.5L12,17Z" />
                                @endif
                            </svg>
                            {{ $patient->gender == 'male' ? 'ذكر' : 'أنثى' }}
                        </span>
                    </td>
                    <td>
                        <div class="text-muted">
                            @if($patient->phone)
                            <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M6.62,10.79C8.06,13.62 10.38,15.94 13.21,17.38L15.41,15.18C15.69,14.9 16.08,14.82 16.43,14.93C17.55,15.3 18.75,15.5 20,15.5A1,1 0 0,1 21,16.5V20A1,1 0 0,1 20,21A17,17 0 0,1 3,4A1,1 0 0,1 4,3H7.5A1,1 0 0,1 8.5,4C8.5,5.25 8.7,6.45 9.07,7.57C9.18,7.92 9.1,8.31 8.82,8.59L6.62,10.79Z" />
                            </svg>
                            {{ $patient->phone }}
                            @else
                            <span class="text-muted">غير محدد</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="text-muted">
                            <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19,3H18V1H16V3H8V1H6V3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M19,19H5V8H19V19Z" />
                            </svg>
                            {{ $patient->created_at->format('Y/m/d') }}
                        </div>
                        <small class="text-muted">{{ $patient->created_at->diffForHumans() }}</small>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('patients.edit', $patient->id) }}"
                                class="btn btn-action btn-edit"
                                title="تعديل">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z" />
                                </svg>
                            </a>
                            <button type="button"
                                class="btn btn-action btn-delete"
                                onclick="confirmDelete('{{ $patient->id }}')"
                                title="حذف">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z" />
                                </svg>
                            </button>
                        </div>

                        <!-- Delete Form -->
                        <form id="delete-form-{{ $patient->id }}"
                            action="{{ route('patients.destroy', $patient->id) }}"
                            method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg width="80" height="80" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 5.5V7.5L12 8.5L9 7.5V5.5L3 7V9L9 10.5V12L3 13.5V15.5L9 14L12 15L15 14L21 15.5V13.5L15 12V10.5L21 9ZM12 10.5C10.9 10.5 10 11.4 10 12.5S10.9 14.5 12 14.5 14 13.6 14 12.5 13.1 10.5 12 10.5Z" />
                                </svg>
                            </div>
                            <h5>لا يوجد مرضى</h5>
                            <p class="text-muted">لم يتم العثور على أي مرضى في النظام</p>
                            <a href="{{ route('patients.create') }}" class="btn btn-primary">
                                <svg class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z" />
                                </svg>إضافة أول مريض
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($patients->hasPages())
    <div class="card-footer bg-light p-4">
        <div class="d-flex justify-content-center">
            @include('components.pagination', ['paginator' => $patients->appends(request()->query())])
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(patientId) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'سيتم حذف المريض نهائياً!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + patientId).submit();
            }
        });
    }
</script>
@endpush