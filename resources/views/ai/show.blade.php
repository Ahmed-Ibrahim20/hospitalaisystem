@extends('dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0"><i class="fas fa-brain me-2"></i>تفاصيل التنبؤ</h1>
                <div>
                    <a href="{{ route('ai.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> العودة للوحة التحكم
                    </a>
                    <a href="{{ route('ai.create') }}" class="btn btn-primary ms-2">
                        <i class="fas fa-plus"></i> تنبؤ جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- معلومات المريض والزيارة -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>معلومات المريض</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>الاسم:</strong></td>
                            <td>{{ $encounter->patient->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>العمر:</strong></td>
                            <td>{{ $encounter->patient->age }} سنة</td>
                        </tr>
                        <tr>
                            <td><strong>الجنس:</strong></td>
                            <td>{{ $encounter->patient->gender === 'male' ? 'ذكر' : 'أنثى' }}</td>
                        </tr>
                        <tr>
                            <td><strong>الهاتف:</strong></td>
                            <td>{{ $encounter->patient->phone }}</td>
                        </tr>
                        <tr>
                            <td><strong>العنوان:</strong></td>
                            <td>{{ $encounter->patient->address }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">🏥 معلومات الزيارة</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>الطبيب:</strong></td>
                            <td>{{ $encounter->doctor->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>تاريخ الزيارة:</strong></td>
                            <td>{{ $encounter->visit_date->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>BMI:</strong></td>
                            <td>{{ $encounter->bmi ?? '25.5' }}</td>
                        </tr>
                        <tr>
                            <td><strong>الوزن:</strong></td>
                            <td>{{ $encounter->weight }} كجم</td>
                        </tr>
                        <tr>
                            <td><strong>الطول:</strong></td>
                            <td>{{ $encounter->height }} سم</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- القياسات الحيوية -->
    @if($encounter->blood_pressure_systolic || $encounter->blood_sugar_fasting)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-heartbeat me-2"></i>القياسات الحيوية</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($encounter->blood_pressure_systolic)
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="text-muted">ضغط الدم</h6>
                                        <h4>{{ $encounter->blood_pressure_systolic }}/{{ $encounter->blood_pressure_diastolic }}</h4>
                                        <small class="text-muted">mmHg</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($encounter->blood_sugar_fasting)
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="text-muted">سكر صائم</h6>
                                        <h4>{{ $encounter->blood_sugar_fasting }}</h4>
                                        <small class="text-muted">mg/dL</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($encounter->blood_sugar_random)
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="text-muted">سكر عشوائي</h6>
                                        <h4>{{ $encounter->blood_sugar_random }}</h4>
                                        <small class="text-muted">mg/dL</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="text-muted">BMI</h6>
                                        <h4>{{ $encounter->bmi ?? '25.5' }}</h4>
                                        <small class="text-muted">kg/m²</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- نتائج التنبؤ -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-robot me-2"></i>نتائج التنبؤ بالذكاء الاصطناعي</h5>
                </div>
                <div class="card-body">
                    @if($encounter->predictions->count() > 0)
                    <div class="row">
                        @foreach($encounter->predictions as $prediction)
                        <div class="col-md-6 mb-3">
                            <div class="card border-{{ $prediction->risk_level === 'high' ? 'danger' : ($prediction->risk_level === 'medium' ? 'warning' : 'success') }}">
                                <div class="card-header bg-{{ $prediction->risk_level === 'high' ? 'danger' : ($prediction->risk_level === 'medium' ? 'warning' : 'success') }} text-white">
                                    <h6 class="mb-0">
                                        {!! $prediction->disease_type === 'diabetes' ? '<i class="fas fa-tint me-1"></i>السكري' : ($prediction->disease_type === 'heart_disease' ? '<i class="fas fa-heart me-1"></i>أمراض القلب' : '<i class="fas fa-tachometer-alt me-1"></i>ضغط الدم') !!}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <p class="mb-2">
                                                <strong>النتيجة:</strong><br>
                                                <span class="badge bg-{{ $prediction->prediction === 1 ? 'danger' : 'success' }} badge-lg">
                                                    {{ $prediction->prediction === 1 ? 'موجود' : 'غير موجود' }}
                                                </span>
                                            </p>
                                            <p class="mb-2">
                                                <strong>الاحتمالية:</strong><br>
                                                <span class="h5">{{ number_format($prediction->probability * 100, 1) }}%</span>
                                            </p>
                                        </div>
                                        <div class="col-6">
                                            <p class="mb-2">
                                                <strong>مستوى الخطر:</strong><br>
                                                <span class="badge bg-{{ $prediction->risk_level === 'high' ? 'danger' : ($prediction->risk_level === 'medium' ? 'warning' : 'success') }} badge-lg">
                                                    {{ $prediction->risk_level === 'high' ? 'عالي' : ($prediction->risk_level === 'medium' ? 'متوسط' : 'منخفض') }}
                                                </span>
                                            </p>
                                            <p class="mb-2">
                                                <strong>الحالة:</strong><br>
                                                <span class="badge bg-{{ $prediction->status === 'pending' ? 'secondary' : ($prediction->status === 'confirmed' ? 'success' : 'danger') }}">
                                                    {{ $prediction->status === 'pending' ? 'منتظر' : ($prediction->status === 'confirmed' ? 'مؤكد' : 'مرفوض') }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>

                                    @if($prediction->confidence_score)
                                    <div class="mt-2">
                                        <small class="text-muted">الثقة: {{ number_format($prediction->confidence_score * 100, 1) }}%</small>
                                    </div>
                                    @endif

                                    @if($prediction->doctor_notes)
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <strong>ملاحظات الطبيب:</strong> {{ $prediction->doctor_notes }}
                                        </small>
                                    </div>
                                    @endif

                                    @if($prediction->status === 'pending')
                                    <div class="mt-3">
                                        <button onclick="reviewPrediction('{{ $prediction->id }}')" class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-check"></i> مراجعة
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-robot fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد تنبؤات لهذه الزيارة</h5>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- التوصيات الطبية -->
    @if($encounter->predictions->where('recommendations', '!=', null)->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>التوصيات الطبية</h5>
                </div>
                <div class="card-body">
                    @foreach($encounter->predictions as $prediction)
                    @if($prediction->recommendations)
                    <div class="mb-3">
                        <h6>{!! $prediction->disease_type === 'diabetes' ? '<i class="fas fa-tint me-1"></i>السكري' : ($prediction->disease_type === 'heart_disease' ? '<i class="fas fa-heart me-1"></i>أمراض القلب' : '<i class="fas fa-tachometer-alt me-1"></i>ضغط الدم') !!}</h6>
                        <ul class="list-unstyled">
                            @foreach(json_decode($prediction->recommendations) as $recommendation)
                            <li class="mb-1">
                                <i class="fas fa-check text-success"></i> {{ $recommendation }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- عوامل الخطر -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">⚠️ عوامل الخطر</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>عوامل الخطر المحددة</h6>
                            @php
                            $riskFactors = [
                            'ضغط دم مرتفع',
                            'مستوى سكر غير طبيعي',
                            'تاريخ عائلي للمرض'
                            ];
                            @endphp
                            @if(count($riskFactors) > 0)
                            <ul class="list-unstyled">
                                @foreach($riskFactors as $factor)
                                <li class="mb-1">
                                    <i class="fas fa-exclamation-triangle text-warning"></i> {{ $factor }}
                                </li>
                                @endforeach
                            </ul>
                            @else
                            <p class="text-muted">لا توجد عوامل خطر محددة</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6>ملخص صحي</h6>
                            @php
                            $healthSummary = [
                            'bmi_status' => 'طبيعي',
                            'blood_pressure_status' => 'مرتفع',
                            'blood_sugar_status' => 'طبيعي'
                            ];
                            @endphp
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>BMI:</strong></td>
                                    <td>{{ $healthSummary['bmi_status'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>ضغط الدم:</strong></td>
                                    <td>{{ $healthSummary['blood_pressure_status'] }}</td>
                                </tr>
                                <tr>
                                    <td><strong>سكر الدم:</strong></td>
                                    <td>{{ $healthSummary['blood_sugar_status'] }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- معلومات إضافية -->
    @if($encounter->symptoms || $encounter->medications || $encounter->allergies || $encounter->family_history)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">📝 معلومات إضافية</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($encounter->symptoms)
                        <div class="col-md-6">
                            <h6>الأعراض</h6>
                            <p>{{ $encounter->symptoms }}</p>
                        </div>
                        @endif
                        @if($encounter->medications)
                        <div class="col-md-6">
                            <h6>الأدوية الحالية</h6>
                            <p>{{ $encounter->medications }}</p>
                        </div>
                        @endif
                        @if($encounter->allergies)
                        <div class="col-md-6">
                            <h6>الحساسية</h6>
                            <p>{{ $encounter->allergies }}</p>
                        </div>
                        @endif
                        @if($encounter->family_history)
                        <div class="col-md-6">
                            <h6>التاريخ المرضي العائلي</h6>
                            <p>{{ $encounter->family_history }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
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