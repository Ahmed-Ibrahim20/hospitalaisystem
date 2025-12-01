@extends('dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0"><i class="fas fa-brain me-2"></i>لوحة تحكم التنبؤات الذكية</h1>
                <div>
                    <a href="{{ route('ai.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> تنبؤ جديد
                    </a>
                    <a href="{{ route('ai.reports') }}" class="btn btn-info ms-2">
                        <i class="fas fa-chart-bar"></i> التقارير
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- إحصائيات سريعة -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['total'] }}</h4>
                            <p class="mb-0">إجمالي التنبؤات</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-pie fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['high_risk'] }}</h4>
                            <p class="mb-0">خطورة عالية</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['medium_risk'] }}</h4>
                            <p class="mb-0">خطورة متوسطة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['low_risk'] }}</h4>
                            <p class="mb-0">خطورة منخفضة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- حالة الخدمة -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>حالة خدمة الذكاء الاصطناعي</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div id="aiHealthStatus">
                                <div class="d-flex align-items-center text-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <span>الخدمة تعمل بشكل جيد (Demo Mode)</span>
                                    <small class="ms-2 text-muted">({{ now()->format('Y-m-d H:i:s') }})</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="badge bg-info fs-6">50ms</div>
                            <small class="ms-2 text-muted">سرعة الاستجابة</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- التنبؤات الأخيرة -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>التنبؤات الأخيرة</h5>
                    <a href="{{ route('ai.pending-review') }}" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-clock"></i> المنتظرة المراجعة
                    </a>
                </div>
                <div class="card-body">
                    @if($recentPredictions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>المريض</th>
                                    <th>الطبيب</th>
                                    <th>نوع المرض</th>
                                    <th>النتيجة</th>
                                    <th>الاحتمالية</th>
                                    <th>مستوى الخطر</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPredictions as $prediction)
                                <tr>
                                    <td>{{ $prediction->encounter->patient->name }}</td>
                                    <td>{{ $prediction->encounter->doctor->name }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ __('diseases.' . $prediction->disease_type, ['disease' => $prediction->disease_type]) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($prediction->prediction == 1)
                                        <span class="badge bg-danger">موجود</span>
                                        @else
                                        <span class="badge bg-success">غير موجود</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($prediction->probability * 100, 1) }}%</td>
                                    <td>
                                        <span class="badge bg-{{ $prediction->risk_level == 'high' ? 'danger' : ($prediction->risk_level == 'medium' ? 'warning' : 'success') }}">
                                            {{ __('risk_levels.' . $prediction->risk_level, ['level' => $prediction->risk_level]) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $prediction->status == 'pending' ? 'secondary' : ($prediction->status == 'confirmed' ? 'success' : 'danger') }}">
                                            {{ __('statuses.' . $prediction->status, ['status' => $prediction->status]) }}
                                        </span>
                                    </td>
                                    <td>{{ $prediction->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('ai.show', $prediction->encounter_id) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($prediction->status == 'pending')
                                            <button onclick="reviewPrediction({{ $prediction->id }})" class="btn btn-outline-warning">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-robot fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد تنبؤات حالياً</h5>
                        <p class="text-muted">ابدأ بإجراء تنبؤ جديد لرؤية النتائج هنا</p>
                        <a href="{{ route('ai.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> تنبؤ جديد
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal مراجعة التنبؤ -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">مراجعة التنبؤ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="reviewForm">
                <div class="modal-body">
                    <input type="hidden" id="predictionId" name="prediction_id">
                    <div class="mb-3">
                        <label for="status" class="form-label">حالة التنبؤ</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">اختر الحالة</option>
                            <option value="confirmed">تأكيد</option>
                            <option value="rejected">رفض</option>
                            <option value="reviewed">تم المراجعة</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="doctor_notes" class="form-label">ملاحظات الطبيب</label>
                        <textarea class="form-control" id="doctor_notes" name="doctor_notes" rows="3" placeholder="أدخل ملاحظاتك..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ المراجعة</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // مراجعة التنبؤ
    function reviewPrediction(predictionId) {
        document.getElementById('predictionId').value = predictionId;
        const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
        modal.show();
    }

    // إرسال نموذج المراجعة
    document.getElementById('reviewForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const predictionId = formData.get('prediction_id');

        fetch(`{{ route('ai.review', ':prediction') }}`.replace(':prediction', predictionId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    status: formData.get('status'),
                    doctor_notes: formData.get('doctor_notes')
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('reviewModal')).hide();
                    location.reload();
                } else {
                    alert('حدث خطأ: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ أثناء المراجعة');
            });
    });
</script>
@endsection