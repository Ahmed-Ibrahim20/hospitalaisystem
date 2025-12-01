@extends('dashboard')

@section('title', 'إدارة الزيارات الطبية')

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #b45309 100%);
        border-radius: 20px;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(245, 158, 11, 0.25);
    }

    .search-card {
        background: var(--card);
        border-radius: 15px;
        border: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
    }

    .encounters-table-card {
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
        background: rgba(245, 158, 11, 0.05);
    }

    .encounter-badge {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.85rem;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .patient-badge {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .doctor-badge {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .date-badge {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
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

    .status-past {
        background: linear-gradient(135deg, #6b7280, #4b5563);
        color: white;
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

    .add-encounter-btn {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 2rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(245, 158, 11, 0.25);
    }

    .add-encounter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(245, 158, 11, 0.35);
        color: white;
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header text-center py-4 mb-4">
    <h1 class="mb-2">
        <svg class="me-3" width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
            <path d="M19.5 3.5L18.1 4.9C19.2 6 19.8 7.5 19.8 9.1C19.8 12.4 17.1 15.1 13.8 15.1S7.8 12.4 7.8 9.1C7.8 7.5 8.4 6 9.5 4.9L8.1 3.5C6.7 4.9 5.8 6.9 5.8 9.1C5.8 13.5 9.4 17.1 13.8 17.1S21.8 13.5 21.8 9.1C21.8 6.9 20.9 4.9 19.5 3.5M7 17.1C6.4 17.1 6 17.5 6 18.1V21C6 21.6 6.4 22 7 22S8 21.6 8 21V18.1C8 17.5 7.6 17.1 7 17.1Z" />
        </svg>
        إدارة الزيارات الطبية
    </h1>
    <p class="mb-3 opacity-90">إدارة وتنظيم جميع الزيارات الطبية والمواعيد</p>
    <a href="{{ route('encounters.create') }}" class="add-encounter-btn">
        <svg class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z" />
        </svg>
        إضافة زيارة جديدة
    </a>
</div>

<!-- Search & Filters -->
<div class="search-card card">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('encounters.index') }}" class="row g-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">
                        <svg class="text-primary" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z" />
                        </svg>
                    </span>
                    <input type="text" name="search" class="form-control form-control-lg"
                        placeholder="ابحث في الزيارات (المريض، الطبيب، التاريخ)..."
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

<!-- Encounters Table -->
<div class="encounters-table-card card">
    <div class="table-responsive">
        <table class="table table-modern">
            <thead>
                <tr>
                    <th width="8%">#</th>
                    <th width="20%">المريض</th>
                    <th width="20%">الطبيب</th>
                    <th width="15%">تاريخ الزيارة</th>
                    <th width="12%">الحالة</th>
                    <th width="10%">الوقت</th>
                    <th width="15%">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($encounters as $encounter)
                <tr>
                    <td>
                        <div class="fw-bold text-primary">#{{ $encounter->id }}</div>
                    </td>
                    <td>
                        <span class="patient-badge">
                            <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 5.5V7.5L12 8.5L9 7.5V5.5L3 7V9L9 10.5V12L3 13.5V15.5L9 14L12 15L15 14L21 15.5V13.5L15 12V10.5L21 9ZM12 10.5C10.9 10.5 10 11.4 10 12.5S10.9 14.5 12 14.5 14 13.6 14 12.5 13.1 10.5 12 10.5Z" />
                            </svg>
                            {{ $encounter->patient->name }}
                        </span>
                    </td>
                    <td>
                        <span class="doctor-badge">
                            <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,2A3,3 0 0,1 15,5V11A3,3 0 0,1 12,14A3,3 0 0,1 9,11V5A3,3 0 0,1 12,2M19,18H5V16C5,13.79 7.58,12 12,12C16.42,12 19,13.79 19,16V18Z" />
                            </svg>
                            {{ $encounter->doctor->name }}
                        </span>
                    </td>
                    <td>
                        <div class="text-muted">
                            <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19,3H18V1H16V3H8V1H6V3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M19,19H5V8H19V19Z" />
                            </svg>
                            {{ \Carbon\Carbon::parse($encounter->visit_date)->format('Y/m/d') }}
                        </div>
                    </td>
                    <td>
                        @php
                        $visitDate = \Carbon\Carbon::parse($encounter->visit_date);
                        $now = \Carbon\Carbon::now();

                        if ($visitDate->isToday()) {
                        $status = 'today';
                        $statusText = 'اليوم';
                        $statusClass = 'status-upcoming';
                        } elseif ($visitDate->isFuture()) {
                        $status = 'upcoming';
                        $statusText = 'قادمة';
                        $statusClass = 'status-upcoming';
                        } else {
                        $status = 'completed';
                        $statusText = 'مكتملة';
                        $statusClass = 'status-completed';
                        }
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            <svg class="me-1" width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                @if($status == 'upcoming' || $status == 'today')
                                <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M16.2,16.2L11,13V7H12.5V12.2L17,14.7L16.2,16.2Z" />
                                @else
                                <path d="M12 2C6.5 2 2 6.5 2 12S6.5 22 12 22 22 17.5 22 12 17.5 2 12 2M10 17L5 12L6.41 10.59L10 14.17L17.59 6.58L19 8L10 17Z" />
                                @endif
                            </svg>
                            {{ $statusText }}
                        </span>
                    </td>
                    <td>
                        <div class="text-muted">
                            {{ \Carbon\Carbon::parse($encounter->visit_date)->format('H:i') }}
                        </div>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($encounter->visit_date)->diffForHumans() }}</small>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('encounters.edit', $encounter->id) }}"
                                class="btn btn-action btn-edit"
                                title="تعديل">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z" />
                                </svg>
                            </a>
                            <button type="button"
                                class="btn btn-action btn-delete"
                                onclick="confirmDelete('{{ $encounter->id }}')"
                                title="حذف">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z" />
                                </svg>
                            </button>
                        </div>

                        <!-- Delete Form -->
                        <form id="delete-form-{{ $encounter->id }}"
                            action="{{ route('encounters.destroy', $encounter->id) }}"
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
                                    <path d="M19.5 3.5L18.1 4.9C19.2 6 19.8 7.5 19.8 9.1C19.8 12.4 17.1 15.1 13.8 15.1S7.8 12.4 7.8 9.1C7.8 7.5 8.4 6 9.5 4.9L8.1 3.5C6.7 4.9 5.8 6.9 5.8 9.1C5.8 13.5 9.4 17.1 13.8 17.1S21.8 13.5 21.8 9.1C21.8 6.9 20.9 4.9 19.5 3.5M7 17.1C6.4 17.1 6 17.5 6 18.1V21C6 21.6 6.4 22 7 22S8 21.6 8 21V18.1C8 17.5 7.6 17.1 7 17.1Z" />
                                </svg>
                            </div>
                            <h5>لا توجد زيارات طبية</h5>
                            <p class="text-muted">لم يتم العثور على أي زيارات طبية في النظام</p>
                            <a href="{{ route('encounters.create') }}" class="btn btn-primary">
                                <svg class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z" />
                                </svg>إضافة أول زيارة
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($encounters->hasPages())
    <div class="card-footer bg-light p-4">
        @include('components.pagination', ['paginator' => $encounters->appends(request()->query())])
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(encounterId) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'سيتم حذف الزيارة الطبية نهائياً!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + encounterId).submit();
            }
        });
    }
</script>
@endpush