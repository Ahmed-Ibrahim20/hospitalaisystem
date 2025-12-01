@extends('dashboard')

@section('title', 'إدارة الصيدلية')

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 50%, #6d28d9 100%);
        border-radius: 20px;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(139, 92, 246, 0.25);
    }

    .search-card {
        background: var(--card);
        border-radius: 15px;
        border: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
    }

    .medicines-table-card {
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
        background: rgba(139, 92, 246, 0.05);
    }

    .medicine-badge {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.85rem;
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: white;
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

    .price-badge {
        background: linear-gradient(135deg, #059669, #047857);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .stock-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .stock-good {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .stock-low {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .stock-out {
        background: linear-gradient(135deg, #ef4444, #dc2626);
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

    .btn-stock {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .btn-stock:hover {
        transform: scale(1.1);
        box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
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

    .add-medicine-btn {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 2rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(139, 92, 246, 0.25);
    }

    .add-medicine-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(139, 92, 246, 0.35);
        color: white;
    }

    .filter-tabs {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .filter-tab {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        border: 2px solid #e5e7eb;
        background: white;
        color: #6b7280;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .filter-tab:hover {
        border-color: #8b5cf6;
        color: #8b5cf6;
    }

    .filter-tab.active {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        border-color: #8b5cf6;
        color: white;
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="page-header text-center py-4 mb-4">
    <h1 class="mb-2">
        <svg class="me-3" width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
            <path d="M6,13C6.55,13 7,13.45 7,14A1,1 0 0,1 6,15A1,1 0 0,1 5,14C5,13.45 5.45,13 6,13M6,17C6.55,17 7,17.45 7,18A1,1 0 0,1 6,19A1,1 0 0,1 5,18C5,17.45 5.45,17 6,17M6,9A1,1 0 0,1 7,10A1,1 0 0,1 6,11A1,1 0 0,1 5,10A1,1 0 0,1 6,9M3,5A2,2 0 0,1 5,3H19A2,2 0 0,1 21,5V19A2,2 0 0,1 19,21H5A2,2 0 0,1 3,19V5M9,7V9H19V7H9M9,11V13H19V11H9M9,15V17H19V15H9Z" />
        </svg>
        إدارة الصيدلية
    </h1>
    <p class="mb-3 opacity-90">إدارة وتنظيم جميع الأدوية والمخزون الطبي</p>
    <a href="{{ route('medicines.create') }}" class="add-medicine-btn">
        <svg class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z" />
        </svg>
        إضافة دواء جديد
    </a>
</div>

<!-- Filter Tabs -->
<div class="filter-tabs">
    <a href="{{ route('medicines.index') }}"
        class="filter-tab {{ !request('filter') ? 'active' : '' }}">
        <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M9,5V9H21V5M9,19H21V15H9M9,14H21V10H9M4,9H8L6,7M4,19H8L6,17M4,14H8L6,12" />
        </svg>
        الكل
    </a>
    <a href="{{ route('medicines.index', ['filter' => 'active']) }}"
        class="filter-tab {{ request('filter') == 'active' ? 'active' : '' }}">
        <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.5 2 2 6.5 2 12S6.5 22 12 22 22 17.5 22 12 17.5 2 12 2M10 17L5 12L6.41 10.59L10 14.17L17.59 6.58L19 8L10 17Z" />
        </svg>
        نشط
    </a>
    <a href="{{ route('medicines.index', ['filter' => 'low_stock']) }}"
        class="filter-tab {{ request('filter') == 'low_stock' ? 'active' : '' }}">
        <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M13,14H11V10H13M13,18H11V16H13M1,21H23L12,2L1,21Z" />
        </svg>
        مخزون منخفض
    </a>
    <a href="{{ route('medicines.index', ['filter' => 'expired']) }}"
        class="filter-tab {{ request('filter') == 'expired' ? 'active' : '' }}">
        <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M16.2,16.2L11,13V7H12.5V12.2L17,14.7L16.2,16.2Z" />
        </svg>
        منتهي الصلاحية
    </a>
</div>

<!-- Search & Filters -->
<div class="search-card card">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('medicines.index') }}" class="row g-3">
            <input type="hidden" name="filter" value="{{ request('filter') }}">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">
                        <svg class="text-primary" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z" />
                        </svg>
                    </span>
                    <input type="text" name="search" class="form-control form-control-lg"
                        placeholder="ابحث في الأدوية (الاسم، الشركة، رقم الدفعة)..."
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

<!-- Medicines Table -->
<div class="medicines-table-card card">
    <div class="table-responsive">
        <table class="table table-modern">
            <thead>
                <tr>
                    <th width="8%">#</th>
                    <th width="20%">اسم الدواء</th>
                    <th width="15%">الشركة المصنعة</th>
                    <th width="10%">السعر</th>
                    <th width="10%">المخزون</th>
                    <th width="12%">تاريخ الانتهاء</th>
                    <th width="10%">الحالة</th>
                    <th width="15%">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($medicines as $medicine)
                <tr>
                    <td>
                        <div class="fw-bold text-primary">#{{ $medicine->id }}</div>
                    </td>
                    <td>
                        <div class="medicine-badge">
                            <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M6,13C6.55,13 7,13.45 7,14A1,1 0 0,1 6,15A1,1 0 0,1 5,14C5,13.45 5.45,13 6,13M6,17C6.55,17 7,17.45 7,18A1,1 0 0,1 6,19A1,1 0 0,1 5,18C5,17.45 5.45,17 6,17M6,9A1,1 0 0,1 7,10A1,1 0 0,1 6,11A1,1 0 0,1 5,10A1,1 0 0,1 6,9M3,5A2,2 0 0,1 5,3H19A2,2 0 0,1 21,5V19A2,2 0 0,1 19,21H5A2,2 0 0,1 3,19V5M9,7V9H19V7H9M9,11V13H19V11H9M9,15V17H19V15H9Z" />
                            </svg>
                            {{ $medicine->name }}
                        </div>
                        @if($medicine->generic_name)
                        <small class="text-muted d-block mt-1">{{ $medicine->generic_name }}</small>
                        @endif
                    </td>
                    <td>
                        <div class="text-muted">
                            {{ $medicine->manufacturer ?? 'غير محدد' }}
                        </div>
                    </td>
                    <td>
                        <span class="price-badge">
                            {{ number_format($medicine->price, 2) }} ج.م
                        </span>
                    </td>
                    <td>
                        @php
                        $stockClass = 'stock-good';
                        if ($medicine->quantity_in_stock == 0) {
                        $stockClass = 'stock-out';
                        } elseif ($medicine->isLowStock()) {
                        $stockClass = 'stock-low';
                        }
                        @endphp
                        <span class="stock-badge {{ $stockClass }}">
                            {{ $medicine->quantity_in_stock }} {{ $medicine->unit }}
                        </span>
                    </td>
                    <td>
                        @if($medicine->expiry_date)
                        <div class="text-muted">
                            <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19,3H18V1H16V3H8V1H6V3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M19,19H5V8H19V19Z" />
                            </svg>
                            {{ $medicine->expiry_date->format('Y/m/d') }}
                        </div>
                        @else
                        <span class="text-muted">غير محدد</span>
                        @endif
                    </td>
                    <td>
                        <span class="status-badge {{ $medicine->getStatusBadgeClass() }}">
                            {{ $medicine->getStatusText() }}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button type="button"
                                class="btn btn-action btn-stock"
                                onclick="updateStock('{{ $medicine->id }}')"
                                title="تحديث المخزون">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z" />
                                </svg>
                            </button>
                            <a href="{{ route('medicines.edit', $medicine->id) }}"
                                class="btn btn-action btn-edit"
                                title="تعديل">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z" />
                                </svg>
                            </a>
                            <button type="button"
                                class="btn btn-action btn-delete"
                                onclick="confirmDelete('{{ $medicine->id }}')"
                                title="حذف">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z" />
                                </svg>
                            </button>
                        </div>

                        <!-- Delete Form -->
                        <form id="delete-form-{{ $medicine->id }}"
                            action="{{ route('medicines.destroy', $medicine->id) }}"
                            method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg width="80" height="80" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M6,13C6.55,13 7,13.45 7,14A1,1 0 0,1 6,15A1,1 0 0,1 5,14C5,13.45 5.45,13 6,13M6,17C6.55,17 7,17.45 7,18A1,1 0 0,1 6,19A1,1 0 0,1 5,18C5,17.45 5.45,17 6,17M6,9A1,1 0 0,1 7,10A1,1 0 0,1 6,11A1,1 0 0,1 5,10A1,1 0 0,1 6,9M3,5A2,2 0 0,1 5,3H19A2,2 0 0,1 21,5V19A2,2 0 0,1 19,21H5A2,2 0 0,1 3,19V5M9,7V9H19V7H9M9,11V13H19V11H9M9,15V17H19V15H9Z" />
                                </svg>
                            </div>
                            <h5>لا توجد أدوية</h5>
                            <p class="text-muted">لم يتم العثور على أي أدوية في النظام</p>
                            <a href="{{ route('medicines.create') }}" class="btn btn-primary">
                                <svg class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z" />
                                </svg>إضافة أول دواء
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($medicines->hasPages())
    <div class="card-footer bg-light p-4">
        @include('components.pagination', ['paginator' => $medicines->appends(request()->query())])
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(medicineId) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'سيتم حذف الدواء نهائياً!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'إلغاء',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + medicineId).submit();
            }
        });
    }

    function updateStock(medicineId) {
        // جلب معلومات الدواء من الصف
        const row = document.querySelector(`tr[data-medicine-id="${medicineId}"]`);
        const medicineName = row ? row.querySelector('td:first-child strong').textContent : 'الدواء';
        const currentStock = row ? row.querySelector('.stock-badge').textContent.split(' ')[0] : '0';

        Swal.fire({
            title: `تحديث مخزون: ${medicineName}`,
            html: `
                <div class="text-start mb-3 p-3 bg-light rounded">
                    <small class="text-muted">الكمية الحالية:</small>
                    <strong class="d-block">${currentStock}</strong>
                </div>
                <div class="mb-3">
                    <label class="form-label text-start d-block">الكمية:</label>
                    <input type="number" id="quantity" class="form-control" min="1" value="1" placeholder="أدخل الكمية">
                </div>
                <div class="mb-3">
                    <label class="form-label text-start d-block">العملية:</label>
                    <select id="operation" class="form-select">
                        <option value="add">إضافة للمخزون ➕</option>
                        <option value="subtract">خصم من المخزون ➖</option>
                    </select>
                </div>
                <div class="alert alert-info text-start">
                    <small>💡 سيتم تحديث المخزون فوراً وإعادة حساب الحالة تلقائياً</small>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '🔄 تحديث المخزون',
            cancelButtonText: '❌ إلغاء',
            confirmButtonColor: '#8b5cf6',
            cancelButtonColor: '#6c757d',
            width: '500px',
            preConfirm: () => {
                const quantity = document.getElementById('quantity').value;
                const operation = document.getElementById('operation').value;

                if (!quantity || quantity < 1) {
                    Swal.showValidationMessage('⚠️ يرجى إدخال كمية صحيحة أكبر من صفر');
                    return false;
                }

                if (operation === 'subtract' && parseInt(quantity) > parseInt(currentStock)) {
                    Swal.showValidationMessage(`⚠️ لا يمكن خصم ${quantity} من المخزون الحالي ${currentStock}`);
                    return false;
                }

                return {
                    quantity: parseInt(quantity),
                    operation: operation
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // عرض loading
                Swal.fire({
                    title: 'جاري التحديث...',
                    text: 'يرجى الانتظار',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                // إرسال طلب AJAX
                fetch(`/medicines/${medicineId}/update-stock`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(result.value)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status) {
                            // نجح التحديث
                            Swal.fire({
                                title: '✅ تم التحديث بنجاح!',
                                html: `
                                    <div class="text-start">
                                        <p><strong>📋 ${data.message}</strong></p>
                                        ${data.data ? `
                                            <div class="mt-3 p-3 bg-light rounded">
                                                <div class="row text-center">
                                                    <div class="col-6">
                                                        <small class="text-muted">الكمية السابقة</small>
                                                        <div class="fw-bold">${data.data.old_quantity} ${data.data.unit}</div>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted">الكمية الجديدة</small>
                                                        <div class="fw-bold text-success">${data.data.new_quantity} ${data.data.unit}</div>
                                                    </div>
                                                </div>
                                                ${data.data.is_low_stock ? '<div class="alert alert-warning mt-2 mb-0"><small>⚠️ تحذير: المخزون منخفض!</small></div>' : ''}
                                                ${data.data.is_out_of_stock ? '<div class="alert alert-danger mt-2 mb-0"><small>🚨 تحذير: نفد المخزون!</small></div>' : ''}
                                            </div>
                                        ` : ''}
                                    </div>
                                `,
                                icon: 'success',
                                confirmButtonText: '🔄 تحديث الصفحة',
                                confirmButtonColor: '#10b981'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            // فشل التحديث
                            Swal.fire({
                                title: '❌ فشل التحديث',
                                text: data.message,
                                icon: 'error',
                                confirmButtonText: 'حسناً',
                                confirmButtonColor: '#ef4444'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: '❌ خطأ في الاتصال',
                            text: 'حدث خطأ أثناء الاتصال بالخادم. يرجى المحاولة مرة أخرى.',
                            icon: 'error',
                            confirmButtonText: 'حسناً',
                            confirmButtonColor: '#ef4444'
                        });
                    });
            }
        });
    }

    // إضافة data attribute للصفوف
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach((row, index) => {
            const updateButton = row.querySelector('.btn-stock');
            if (updateButton) {
                const medicineId = updateButton.getAttribute('onclick').match(/updateStock\('(\d+)'\)/)[1];
                row.setAttribute('data-medicine-id', medicineId);
            }
        });
    });
</script>
@endpush