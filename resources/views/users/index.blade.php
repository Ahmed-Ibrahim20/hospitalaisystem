@extends('dashboard')

@section('title', 'إدارة المستخدمين')

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 50%, #059669 100%);
        border-radius: 20px;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(14, 165, 233, 0.25);
    }

    .search-card {
        background: var(--card);
        border-radius: 15px;
        border: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
    }

    .users-table-card {
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
        background: rgba(14, 165, 233, 0.05);
    }

    .user-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .user-name {
        font-weight: 600;
        color: var(--text);
        margin: 0;
    }

    .user-email {
        font-size: 0.9rem;
        color: var(--muted);
        margin: 0;
    }

    .role-badge {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .role-admin {
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
        color: white;
    }

    .role-doctor {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .role-nurse {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .role-default {
        background: linear-gradient(135deg, #64748b, #475569);
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

    .add-user-btn {
        background: linear-gradient(135deg, #0ea5e9, #059669);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 2rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(14, 165, 233, 0.25);
    }

    .add-user-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(14, 165, 233, 0.35);
        color: white;
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header text-center py-4 mb-4">
    <h1 class="mb-2">
        <svg class="me-3" width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12,5.5A3.5,3.5 0 0,1 15.5,9A3.5,3.5 0 0,1 12,12.5A3.5,3.5 0 0,1 8.5,9A3.5,3.5 0 0,1 12,5.5M5,8C5.56,8 6.08,8.15 6.53,8.42C6.38,9.85 6.8,11.27 7.66,12.38C7.16,13.34 6.16,14 5,14A3,3 0 0,1 2,11A3,3 0 0,1 5,8M19,8A3,3 0 0,1 22,11A3,3 0 0,1 19,14C17.84,14 16.84,13.34 16.34,12.38C17.2,11.27 17.62,9.85 17.47,8.42C17.92,8.15 18.44,8 19,8M5.5,18.25C5.5,16.18 8.41,14.5 12,14.5C15.59,14.5 18.5,16.18 18.5,18.25V20H5.5V18.25M0,20V18.5C0,17.11 1.89,15.94 4.45,15.6C3.86,16.28 3.5,17.22 3.5,18.25V20H0M24,20H20.5V18.25C20.5,17.22 20.14,16.28 19.55,15.6C22.11,15.94 24,17.11 24,18.5V20Z" />
        </svg>
        إدارة المستخدمين
    </h1>
    <p class="mb-3 opacity-90">إدارة وتنظيم جميع مستخدمي النظام وأدوارهم</p>
    <a href="{{ route('users.create') }}" class="add-user-btn">
        <svg class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z" />
        </svg>
        إضافة مستخدم جديد
    </a>
</div>

<!-- Search & Filters -->
<div class="search-card card">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('users.index') }}" class="row g-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">
                        <svg class="text-primary" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z" />
                        </svg>
                    </span>
                    <input type="text" name="search" class="form-control form-control-lg"
                        placeholder="ابحث بالاسم أو البريد الإلكتروني..."
                        value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="perPage" class="form-select form-select-lg" onchange="this.form.submit()">
                    <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>
                        <i class="fa-solid fa-list"></i> 10 عناصر
                    </option>
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

<!-- Users Table -->
<div class="users-table-card card">
    <div class="table-responsive">
        <table class="table table-modern">
            <thead>
                <tr>
                    <th width="8%">#</th>
                    <th width="30%">بيانات المستخدم</th>
                    <th width="20%">الدور</th>
                    <th width="15%">تاريخ الانضمام</th>
                    <th width="15%">حالة الحساب</th>
                    <th width="12%">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="fw-bold text-primary">#{{ $user->id }}</div>
                    </td>
                    <td>
                        <div class="user-info">
                            <img class="user-avatar"
                                src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=667eea&color=fff&size=128"
                                alt="{{ $user->name }}">
                            <div>
                                <div class="user-name">{{ $user->name }}</div>
                                <div class="user-email">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($user->role)
                        @php
                        $roleClass = match($user->role->name) {
                        'admin' => 'role-admin',
                        'doctor' => 'role-doctor',
                        'nurse' => 'role-nurse',
                        default => 'role-default'
                        };
                        @endphp
                        <span class="role-badge {{ $roleClass }}">
                            @if($user->role->name == 'doctor')
                            <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,7C13.4,7 14.8,8.6 14.8,10V11.5C14.8,12.4 14.4,13.2 13.7,13.7V16.5C13.7,17.1 13.3,17.5 12.7,17.5H11.3C10.7,17.5 10.3,17.1 10.3,16.5V13.7C9.6,13.2 9.2,12.4 9.2,11.5V10C9.2,8.6 10.6,7 12,7Z" />
                            </svg>
                            دكتور
                            @elseif($user->role->name == 'admin')
                            <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1M12,7C13.4,7 14.8,8.6 14.8,10V11.5C14.8,12.4 14.4,13.2 13.7,13.7V16.5C13.7,17.1 13.3,17.5 12.7,17.5H11.3C10.7,17.5 10.3,17.1 10.3,16.5V13.7C9.6,13.2 9.2,12.4 9.2,11.5V10C9.2,8.6 10.6,7 12,7Z" />
                            </svg>
                            مدير
                            @elseif($user->role->name == 'nurse')
                            ممرض
                            @elseif($user->role->name == 'receptionist')
                            موظف استقبال
                            @elseif($user->role->name == 'pharmacist')
                            صيدلي
                            @else
                            {{ $user->role->name }}
                            @endif
                        </span>
                        @else
                        <span class="role-badge role-default">غير محدد</span>
                        @endif
                    </td>
                    <td>
                        <div class="text-muted">
                            <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19,3H18V1H16V3H8V1H6V3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M19,19H5V8H19V19Z" />
                            </svg>
                            {{ $user->created_at->format('Y/m/d') }}
                        </div>
                        <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                    </td>
                    <td>
                        @if($user->email_verified_at)
                        <span class="badge bg-success">
                            <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" />
                            </svg>مفعل
                        </span>
                        @else
                        <span class="badge bg-warning">
                            <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M16.2,16.2L11,13V7H12.5V12.2L17,14.7L16.2,16.2Z" />
                            </svg>معلق
                        </span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('users.edit', $user->id) }}"
                                class="btn btn-action btn-edit"
                                title="تعديل">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z" />
                                </svg>
                            </a>
                            <button type="button"
                                class="btn btn-action btn-delete"
                                onclick="confirmDelete('{{ $user->id }}')"
                                title="حذف">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z" />
                                </svg>
                            </button>
                        </div>

                        <!-- Delete Form -->
                        <form id="delete-form-{{ $user->id }}"
                            action="{{ route('users.destroy', $user->id) }}"
                            method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg width="80" height="80" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12,5.5A3.5,3.5 0 0,1 15.5,9A3.5,3.5 0 0,1 12,12.5A3.5,3.5 0 0,1 8.5,9A3.5,3.5 0 0,1 12,5.5M5,8C5.56,8 6.08,8.15 6.53,8.42C6.38,9.85 6.8,11.27 7.66,12.38C7.16,13.34 6.16,14 5,14A3,3 0 0,1 2,11A3,3 0 0,1 5,8M19,8A3,3 0 0,1 22,11A3,3 0 0,1 19,14C17.84,14 16.84,13.34 16.34,12.38C17.2,11.27 17.62,9.85 17.47,8.42C17.92,8.15 18.44,8 19,8M5.5,18.25C5.5,16.18 8.41,14.5 12,14.5C15.59,14.5 18.5,16.18 18.5,18.25V20H5.5V18.25M0,20V18.5C0,17.11 1.89,15.94 4.45,15.6C3.86,16.28 3.5,17.22 3.5,18.25V20H0M24,20H20.5V18.25C20.5,17.22 20.14,16.28 19.55,15.6C22.11,15.94 24,17.11 24,18.5V20Z" />
                                </svg>
                            </div>
                            <h5>لا توجد مستخدمين</h5>
                            <p class="text-muted">لم يتم العثور على أي مستخدمين في النظام</p>
                            <a href="{{ route('users.create') }}" class="btn btn-primary">
                                <svg class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z" />
                                </svg>إضافة أول مستخدم
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
    <div class="card-footer bg-light p-4">
        @include('components.pagination', ['paginator' => $users->appends(request()->query())])
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(userId) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'سيتم حذف المستخدم نهائياً!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + userId).submit();
            }
        });
    }
</script>
@endpush