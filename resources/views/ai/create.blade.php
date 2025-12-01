@extends('dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0"><i class="fas fa-brain me-2"></i>تنبؤ جديد بالأمراض</h1>
                <a href="{{ route('ai.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> العودة للوحة التحكم
                </a>
            </div>
        </div>
    </div>

    <form id="predictionForm">
        @csrf
        <div class="row">
            <!-- معلومات المريض والطبيب -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>معلومات المريض والطبيب</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="patient_id" class="form-label">المريض</label>
                            <select class="form-select" id="patient_id" name="patient_id">
                                <option value="">اختر المريض</option>
                                @foreach($patients as $patient)
                                <option value="{{ $patient->id }}"
                                    data-age="{{ $patient->age }}"
                                    data-gender="{{ $patient->gender }}">
                                    {{ $patient->name }} ({{ $patient->age }} سنة)
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="doctor_id" class="form-label">الطبيب</label>
                            <select class="form-select" id="doctor_id" name="doctor_id">
                                <option value="">اختر الطبيب</option>
                                @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">الأمراض المطلوبة للتنبؤ</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="diseases[]"
                                            value="diabetes" id="disease_diabetes" checked>
                                        <label class="form-check-label" for="disease_diabetes">
                                            <i class="fas fa-tint me-1"></i> السكري
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="diseases[]"
                                            value="heart_disease" id="disease_heart">
                                        <label class="form-check-label" for="disease_heart">
                                            <i class="fas fa-heart me-1"></i> أمراض القلب
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="diseases[]"
                                            value="hypertension" id="disease_hypertension">
                                        <label class="form-check-label" for="disease_hypertension">
                                            <i class="fas fa-tachometer-alt me-1"></i> ضغط الدم
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- القياسات الحيوية -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-heartbeat me-2"></i>القياسات الحيوية</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="weight" class="form-label">الوزن (كجم)</label>
                                    <input type="number" class="form-control" id="weight" name="weight"
                                        step="0.1" min="20" max="300">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="height" class="form-label">الطول (سم)</label>
                                    <input type="number" class="form-control" id="height" name="height"
                                        step="0.1" min="50" max="250">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="blood_pressure_systolic" class="form-label">ضغط الدم الانقباضي</label>
                                    <input type="number" class="form-control" id="blood_pressure_systolic"
                                        name="blood_pressure_systolic" min="70" max="250">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="blood_pressure_diastolic" class="form-label">ضغط الدم الانبساطي</label>
                                    <input type="number" class="form-control" id="blood_pressure_diastolic"
                                        name="blood_pressure_diastolic" min="40" max="150">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="blood_sugar_fasting" class="form-label">سكر الدم صائماً</label>
                                    <input type="number" class="form-control" id="blood_sugar_fasting"
                                        name="blood_sugar_fasting" min="50" max="400">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="blood_sugar_random" class="form-label">سكر الدم عشوائي</label>
                                    <input type="number" class="form-control" id="blood_sugar_random"
                                        name="blood_sugar_random" min="50" max="500">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="bmi" class="form-label">مؤشر كتلة الجسم (BMI)</label>
                            <input type="number" class="form-control" id="bmi" name="bmi"
                                step="0.1" min="10" max="100" readonly>
                            <small class="text-muted">يحسب تلقائياً من الوزن والطول</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- استبيان عوامل الخطر - BRFSS Questions -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">🔍 استبيان عوامل الخطر (BRFSS)</h5>
                        <small class="text-muted">أسئلة استبيان عوامل الخطر السلوكية للولايات المتحدة</small>
                    </div>
                    <div class="card-body">
                        <!-- السطر الأول -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>هل أخبرك الطبيب أنك تعاني من ضغط دم مرتفع؟</strong>
                                        <br><small class="text-muted">HighBP</small>
                                    </label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="high_bp" id="high_bp_0" value="0" checked>
                                        <label class="btn btn-outline-danger" for="high_bp_0">لا</label>
                                        <input type="radio" class="btn-check" name="high_bp" id="high_bp_1" value="1">
                                        <label class="btn btn-outline-success" for="high_bp_1">نعم</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>هل أخبرك الطبيب أنك تعاني من كوليسترول عالي؟</strong>
                                        <br><small class="text-muted">HighChol</small>
                                    </label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="high_chol" id="high_chol_0" value="0" checked>
                                        <label class="btn btn-outline-danger" for="high_chol_0">لا</label>
                                        <input type="radio" class="btn-check" name="high_chol" id="high_chol_1" value="1">
                                        <label class="btn btn-outline-success" for="high_chol_1">نعم</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>هل قمت بفحص الكوليسترول في السنوات الخمس الماضية؟</strong>
                                        <br><small class="text-muted">CholCheck</small>
                                    </label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="chol_check" id="chol_check_0" value="0">
                                        <label class="btn btn-outline-danger" for="chol_check_0">لا</label>
                                        <input type="radio" class="btn-check" name="chol_check" id="chol_check_1" value="1" checked>
                                        <label class="btn btn-outline-success" for="chol_check_1">نعم</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- السطر الثاني -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>هل تدخن السجائر حالياً؟</strong>
                                        <br><small class="text-muted">Smoker</small>
                                    </label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="smoker" id="smoker_0" value="0" checked>
                                        <label class="btn btn-outline-danger" for="smoker_0">لا</label>
                                        <input type="radio" class="btn-check" name="smoker" id="smoker_1" value="1">
                                        <label class="btn btn-outline-success" for="smoker_1">نعم</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>هل أصبت بسكتة دماغية من قبل؟</strong>
                                        <br><small class="text-muted">Stroke</small>
                                    </label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="stroke" id="stroke_0" value="0" checked>
                                        <label class="btn btn-outline-danger" for="stroke_0">لا</label>
                                        <input type="radio" class="btn-check" name="stroke" id="stroke_1" value="1">
                                        <label class="btn btn-outline-success" for="stroke_1">نعم</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>هل أصبت بأمراض القلب أو نوبة قلبية؟</strong>
                                        <br><small class="text-muted">HeartDiseaseorAttack</small>
                                    </label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="heart_disease" id="heart_disease_0" value="0" checked>
                                        <label class="btn btn-outline-danger" for="heart_disease_0">لا</label>
                                        <input type="radio" class="btn-check" name="heart_disease" id="heart_disease_1" value="1">
                                        <label class="btn btn-outline-success" for="heart_disease_1">نعم</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- السطر الثالث -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>هل تمارس نشاطاً بدنياً أو تمارين رياضية؟</strong>
                                        <br><small class="text-muted">PhysActivity</small>
                                    </label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="phys_activity" id="phys_activity_0" value="0">
                                        <label class="btn btn-outline-danger" for="phys_activity_0">لا</label>
                                        <input type="radio" class="btn-check" name="phys_activity" id="phys_activity_1" value="1" checked>
                                        <label class="btn btn-outline-success" for="phys_activity_1">نعم</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>هل تتناول الفواكه مرة واحدة على الأقل يومياً؟</strong>
                                        <br><small class="text-muted">Fruits</small>
                                    </label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="fruits" id="fruits_0" value="0">
                                        <label class="btn btn-outline-danger" for="fruits_0">لا</label>
                                        <input type="radio" class="btn-check" name="fruits" id="fruits_1" value="1" checked>
                                        <label class="btn btn-outline-success" for="fruits_1">نعم</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>هل تتناول الخضروات مرة واحدة على الأقل يومياً؟</strong>
                                        <br><small class="text-muted">Veggies</small>
                                    </label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="veggies" id="veggies_0" value="0">
                                        <label class="btn btn-outline-danger" for="veggies_0">لا</label>
                                        <input type="radio" class="btn-check" name="veggies" id="veggies_1" value="1" checked>
                                        <label class="btn btn-outline-success" for="veggies_1">نعم</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- السطر الرابع -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>هل تستهلك المشروبات الكحولية بكثرة؟</strong>
                                        <br><small class="text-muted">HvyAlcoholConsump</small>
                                    </label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="heavy_alcohol" id="heavy_alcohol_0" value="0" checked>
                                        <label class="btn btn-outline-danger" for="heavy_alcohol_0">لا</label>
                                        <input type="radio" class="btn-check" name="heavy_alcohol" id="heavy_alcohol_1" value="1">
                                        <label class="btn btn-outline-success" for="heavy_alcohol_1">نعم</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>هل لديك أي نوع من التأمين الصحي؟</strong>
                                        <br><small class="text-muted">AnyHealthcare</small>
                                    </label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="any_healthcare" id="any_healthcare_0" value="0">
                                        <label class="btn btn-outline-danger" for="any_healthcare_0">لا</label>
                                        <input type="radio" class="btn-check" name="any_healthcare" id="any_healthcare_1" value="1" checked>
                                        <label class="btn btn-outline-success" for="any_healthcare_1">نعم</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>هل هناك مرة في العام الماضي لم تتمكن من رؤية الطبيب بسبب التكلفة؟</strong>
                                        <br><small class="text-muted">NoDocbcCost</small>
                                    </label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="no_doc_cost" id="no_doc_cost_0" value="0" checked>
                                        <label class="btn btn-outline-danger" for="no_doc_cost_0">لا</label>
                                        <input type="radio" class="btn-check" name="no_doc_cost" id="no_doc_cost_1" value="1">
                                        <label class="btn btn-outline-success" for="no_doc_cost_1">نعم</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- السطر الخامس - الصحة العامة -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="gen_health" class="form-label">
                                        <strong>كيف تصف صحتك العامة؟</strong>
                                        <br><small class="text-muted">GenHlth (1=ممتازة - 5=سيئة)</small>
                                    </label>
                                    <select class="form-select" id="gen_health" name="gen_health">
                                        <option value="1">1 - ممتازة</option>
                                        <option value="2">2 - جيدة جداً</option>
                                        <option value="3" selected>3 - جيدة</option>
                                        <option value="4">4 - متوسطة</option>
                                        <option value="5">5 - سيئة</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ment_health" class="form-label">
                                        <strong>عدد الأيام التي كانت صحتك النفسية سيئة في الشهر الماضي؟</strong>
                                        <br><small class="text-muted">MentHlth (0-30 يوم)</small>
                                    </label>
                                    <input type="number" class="form-control" id="ment_health" name="ment_health"
                                        min="0" max="30" value="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="phys_health" class="form-label">
                                        <strong>عدد الأيام التي كانت صحتك الجسدية سيئة في الشهر الماضي؟</strong>
                                        <br><small class="text-muted">PhysHlth (0-30 يوم)</small>
                                    </label>
                                    <input type="number" class="form-control" id="phys_health" name="phys_health"
                                        min="0" max="30" value="0">
                                </div>
                            </div>
                        </div>

                        <!-- السطر السادس -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <strong>هل تواجه صعوبة في المشي أو صعود الدرج؟</strong>
                                        <br><small class="text-muted">DiffWalk</small>
                                    </label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="diff_walking" id="diff_walking_0" value="0" checked>
                                        <label class="btn btn-outline-danger" for="diff_walking_0">لا</label>
                                        <input type="radio" class="btn-check" name="diff_walking" id="diff_walking_1" value="1">
                                        <label class="btn btn-outline-success" for="diff_walking_1">نعم</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="education" class="form-label">
                                        <strong>المستوى التعليمي</strong>
                                        <br><small class="text-muted">Education (1=أقل من ثانوي - 6=دكتوراه)</small>
                                    </label>
                                    <select class="form-select" id="education" name="education">
                                        <option value="1">1 - أقل من ثانوي</option>
                                        <option value="2">2 - ثانوي</option>
                                        <option value="3">3 - دبلوم</option>
                                        <option value="4" selected>4 - بكالوريوس</option>
                                        <option value="5">5 - ماجستير</option>
                                        <option value="6">6 - دكتوراه</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="income" class="form-label">
                                        <strong>الدخل السنوي للأسرة</strong>
                                        <br><small class="text-muted">Income (1=أقل من 10 آلاف - 8=75+ ألف)</small>
                                    </label>
                                    <select class="form-select" id="income" name="income">
                                        <option value="1">1 - أقل من 10 آلاف دولار</option>
                                        <option value="2">2 - 10-15 ألف دولار</option>
                                        <option value="3">3 - 15-20 ألف دولار</option>
                                        <option value="4">4 - 20-25 ألف دولار</option>
                                        <option value="5" selected>5 - 25-35 ألف دولار</option>
                                        <option value="6">6 - 35-50 ألف دولار</option>
                                        <option value="7">7 - 50-75 ألف دولار</option>
                                        <option value="8">8 - 75+ ألف دولار</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- معلومات إضافية -->
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">📝 الأعراض</h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" name="symptoms" rows="4"
                            placeholder="صف الأعراض التي يشعر بها المريض..."></textarea>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">💊 الأدوية الحالية</h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" name="medications" rows="4"
                            placeholder="اذكر الأدوية التي يتناولها المريض حالياً..."></textarea>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <5 class="mb-0">🧬 التاريخ المرضي</h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" name="family_history" rows="4"
                            placeholder="اذكر الأمراض الوراثية في العائلة..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- أمثلة توضيحية -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">📋 أمثلة توضيحية - بيانات تجريبية جاهزة</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- مثال 1: مريض سكري محتمل -->
                            <div class="col-md-4 mb-3">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0"><i class="fas fa-user me-1"></i>مريض سكري محتمل</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-2"><strong>البيانات:</strong></p>
                                        <ul class="small">
                                            <li>العمر: 45 سنة</li>
                                            <li>الوزن: 95 كجم</li>
                                            <li>الطول: 170 سم</li>
                                            <li>BMI: 32.9</li>
                                            <li>ضغط الدم: 140/90</li>
                                            <li>سكر صائم: 135</li>
                                            <li>ضغط دم مرتفع: نعم</li>
                                            <li>نشاط بدني: لا</li>
                                        </ul>
                                        <button type="button" class="btn btn-warning btn-sm" onclick="fillExample1()">
                                            <i class="fas fa-fill"></i> ملء البيانات
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- مثال 2: مريض قلب محتمل -->
                            <div class="col-md-4 mb-3">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h6 class="mb-0"><i class="fas fa-heart me-1"></i>مريض قلب محتمل</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-2"><strong>البيانات:</strong></p>
                                        <ul class="small">
                                            <li>العمر: 58 سنة</li>
                                            <li>الوزن: 88 كجم</li>
                                            <li>الطول: 175 سم</li>
                                            <li>BMI: 28.7</li>
                                            <li>ضغط الدم: 155/95</li>
                                            <li>كوليسترول عالي: نعم</li>
                                            <li>مدخن: نعم</li>
                                            <li>أمراض قلبية: نعم</li>
                                        </ul>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="fillExample2()">
                                            <i class="fas fa-fill"></i> ملء البيانات
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- مثال 3: مريض ضغط دم محتمل -->
                            <div class="col-md-4 mb-3">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0"><i class="fas fa-tachometer-alt me-1"></i>مريض ضغط دم محتمل</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-2"><strong>البيانات:</strong></p>
                                        <ul class="small">
                                            <li>العمر: 52 سنة</li>
                                            <li>الوزن: 82 كجم</li>
                                            <li>الطول: 168 سم</li>
                                            <li>BMI: 29.1</li>
                                            <li>ضغط الدم: 145/92</li>
                                            <li>ضغط دم مرتفع: نعم</li>
                                            <li>تاريخ عائلي: نعم</li>
                                            <li>ملح عالي: نعم</li>
                                        </ul>
                                        <button type="button" class="btn btn-info btn-sm" onclick="fillExample3()">
                                            <i class="fas fa-fill"></i> ملء البيانات
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- مثال 4: شخص سليم -->
                            <div class="col-md-4 mb-3">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0"><i class="fas fa-heart me-1"></i>شخص سليم</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-2"><strong>البيانات:</strong></p>
                                        <ul class="small">
                                            <li>العمر: 28 سنة</li>
                                            <li>الوزن: 70 كجم</li>
                                            <li>الطول: 175 سم</li>
                                            <li>BMI: 22.9</li>
                                            <li>ضغط الدم: 120/80</li>
                                            <li>سكر صائم: 85</li>
                                            <li>نشاط بدني: نعم</li>
                                            <li>فواكه وخضروات: نعم</li>
                                        </ul>
                                        <button type="button" class="btn btn-success btn-sm" onclick="fillExample4()">
                                            <i class="fas fa-fill"></i> ملء البيانات
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- مثال 5: مريض متعدد الأمراض -->
                            <div class="col-md-4 mb-3">
                                <div class="card border-dark">
                                    <div class="card-header bg-dark text-white">
                                        <h6 class="mb-0"><i class="fas fa-procedures me-1"></i>مريض متعدد الأمراض</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-2"><strong>البيانات:</strong></p>
                                        <ul class="small">
                                            <li>العمر: 65 سنة</li>
                                            <li>الوزن: 105 كجم</li>
                                            <li>الطول: 165 سم</li>
                                            <li>BMI: 38.5</li>
                                            <li>ضغط الدم: 170/100</li>
                                            <li>سكر صائم: 180</li>
                                            <li>كل الأمراض: نعم</li>
                                            <li>نشاط بدني: لا</li>
                                        </ul>
                                        <button type="button" class="btn btn-dark btn-sm" onclick="fillExample5()">
                                            <i class="fas fa-fill"></i> ملء البيانات
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- مثال 6: سيدة حامل -->
                            <div class="col-md-4 mb-3">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0"><i class="fas fa-female me-1"></i>سيدة حامل</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-2"><strong>البيانات:</strong></p>
                                        <ul class="small">
                                            <li>العمر: 32 سنة</li>
                                            <li>الوزن: 78 كجم</li>
                                            <li>الطول: 162 سم</li>
                                            <li>BMI: 29.7</li>
                                            <li>ضغط الدم: 128/85</li>
                                            <li>سكر صائم: 92</li>
                                            <li>حامل: نعم</li>
                                            <li>سكري حملي: محتمل</li>
                                        </ul>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="fillExample6()">
                                            <i class="fas fa-fill"></i> ملء البيانات
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- زر الإرسال -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-primary btn-lg px-5" id="submitBtn">
                            <i class="fas fa-robot"></i> إجراء التنبؤ
                        </button>
                        <div id="loadingIndicator" class="mt-3" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">جاري إجراء التنبؤ...</span>
                            </div>
                            <p class="mt-2">جاري إجراء التنبؤ بالذكاء الاصطناعي...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- نتائج التنبؤ -->
<div class="modal fade" id="resultsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-robot me-2"></i>نتائج التنبؤ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="resultsContent">
                <!-- سيتم عرض النتائج هنا -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <a href="#" class="btn btn-primary" id="viewDetailsBtn">عرض التفاصيل</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // حساب BMI تلقائياً
    function calculateBMI() {
        const weight = parseFloat(document.getElementById('weight').value);
        const height = parseFloat(document.getElementById('height').value);

        if (weight && height) {
            const heightInMeters = height / 100;
            const bmi = weight / (heightInMeters * heightInMeters);
            document.getElementById('bmi').value = bmi.toFixed(2);
        }
    }

    document.getElementById('weight').addEventListener('input', calculateBMI);
    document.getElementById('height').addEventListener('input', calculateBMI);

    // ملء أمثلة البيانات
    function fillExample1() {
        // اختيار مريض وطبيب افتراضي
        const patientSelect = document.getElementById('patient_id');
        const doctorSelect = document.getElementById('doctor_id');

        if (patientSelect && patientSelect.options.length > 1) {
            patientSelect.selectedIndex = 1;
        }
        if (doctorSelect && doctorSelect.options.length > 1) {
            doctorSelect.selectedIndex = 1;
        }

        // ملء القياسات الحيوية
        document.getElementById('weight').value = 95;
        document.getElementById('height').value = 170;
        document.getElementById('blood_pressure_systolic').value = 140;
        document.getElementById('blood_pressure_diastolic').value = 90;
        document.getElementById('blood_sugar_fasting').value = 135;
        document.getElementById('blood_sugar_random').value = 160;
        document.getElementById('high_bp_1').checked = true;
        document.getElementById('phys_activity_0').checked = true;
        document.getElementById('gen_health').value = 4;
        document.getElementById('ment_health').value = 15;
        document.getElementById('phys_health').value = 10;
        document.getElementById('diff_walking_1').checked = true;
        document.getElementById('income').value = 3;

        // إضافة الحقول المفقودة
        document.getElementById('high_chol_0').checked = true;
        document.getElementById('chol_check_1').checked = true;
        document.getElementById('smoker_0').checked = true;
        document.getElementById('stroke_0').checked = true;
        document.getElementById('heart_disease_0').checked = true;
        document.getElementById('fruits_0').checked = true;
        document.getElementById('veggies_0').checked = true;
        document.getElementById('heavy_alcohol_0').checked = true;
        document.getElementById('any_healthcare_1').checked = true;
        document.getElementById('no_doc_cost_0').checked = true;
        document.getElementById('education').value = 2;

        const symptomsTextarea = document.querySelector('textarea[name="symptoms"]');
        const medicationsTextarea = document.querySelector('textarea[name="medications"]');
        const familyHistoryTextarea = document.querySelector('textarea[name="family_history"]');

        if (symptomsTextarea) {
            symptomsTextarea.value = 'تعب شديد، عطش مستمر، كثرة التبول، جفاف';
        }
        if (medicationsTextarea) {
            medicationsTextarea.value = 'لا يوجد أدوية حالياً';
        }
        if (familyHistoryTextarea) {
            familyHistoryTextarea.value = 'الأب مصاب بالسكري، الأم مصابة بارتفاع ضغط الدم';
        }

        // اختيار مرض السكري تلقائياً
        document.getElementById('disease_diabetes').checked = true;
        document.getElementById('disease_heart').checked = false;
        document.getElementById('disease_hypertension').checked = false;

        calculateBMI();

        setTimeout(() => {
            document.getElementById('predictionForm').dispatchEvent(new Event('submit'));
        }, 500);
    }

    function fillExample2() {
        // اختيار مريض وطبيب افتراضي
        const patientSelect = document.getElementById('patient_id');
        const doctorSelect = document.getElementById('doctor_id');
        if (patientSelect.options.length > 1) patientSelect.selectedIndex = 1;
        if (doctorSelect.options.length > 1) doctorSelect.selectedIndex = 1;

        document.getElementById('weight').value = 88;
        document.getElementById('height').value = 175;
        document.getElementById('blood_pressure_systolic').value = 155;
        document.getElementById('blood_pressure_diastolic').value = 95;
        document.getElementById('blood_sugar_fasting').value = 110;
        document.getElementById('blood_sugar_random').value = 140;
        document.getElementById('high_chol_1').checked = true;
        document.getElementById('smoker_1').checked = true;
        document.getElementById('heart_disease_1').checked = true;
        document.getElementById('phys_activity_0').checked = true;
        document.getElementById('gen_health').value = 4;
        document.getElementById('ment_health').value = 20;
        document.getElementById('phys_health').value = 15;
        document.getElementById('diff_walking_1').checked = true;
        document.getElementById('income').value = 4;

        // إضافة الحقول المفقودة
        document.getElementById('high_bp_0').checked = true;
        document.getElementById('chol_check_1').checked = true;
        document.getElementById('stroke_0').checked = true;
        document.getElementById('fruits_0').checked = true;
        document.getElementById('veggies_0').checked = true;
        document.getElementById('heavy_alcohol_0').checked = true;
        document.getElementById('any_healthcare_1').checked = true;
        document.getElementById('no_doc_cost_0').checked = true;
        document.getElementById('education').value = 3;
        document.querySelector('textarea[name="symptoms"]').value = 'ألم في الصدر، ضيق في التنفس، خفقان القلب، تعب';
        document.querySelector('textarea[name="medications"]').value = 'أسبرين، أدوية خفض الكوليسترول';
        document.querySelector('textarea[name="family_history"]').value = 'الأب أصيب بنوبة قلبية، تاريخ عائلي لأمراض القلب';

        // اختيار مرض القلب تلقائياً
        document.getElementById('disease_diabetes').checked = false;
        document.getElementById('disease_heart').checked = true;
        document.getElementById('disease_hypertension').checked = false;

        calculateBMI();

        // إرسال النموذج تلقائياً بعد ملء البيانات
        setTimeout(() => {
            document.getElementById('predictionForm').dispatchEvent(new Event('submit'));
        }, 500);
    }

    function fillExample3() {
        // اختيار مريض وطبيب افتراضي
        const patientSelect = document.getElementById('patient_id');
        const doctorSelect = document.getElementById('doctor_id');
        if (patientSelect.options.length > 1) patientSelect.selectedIndex = 1;
        if (doctorSelect.options.length > 1) doctorSelect.selectedIndex = 1;

        document.getElementById('weight').value = 90;
        document.getElementById('height').value = 165;
        document.getElementById('blood_pressure_systolic').value = 160;
        document.getElementById('blood_pressure_diastolic').value = 100;
        document.getElementById('blood_sugar_fasting').value = 120;
        document.getElementById('blood_sugar_random').value = 150;
        document.getElementById('high_bp_1').checked = true;
        document.getElementById('high_chol_1').checked = true;
        document.getElementById('smoker_1').checked = true;
        document.getElementById('phys_activity_0').checked = true;
        document.getElementById('gen_health').value = 5;
        document.getElementById('ment_health').value = 10;
        document.getElementById('phys_health').value = 20;
        document.getElementById('diff_walking_1').checked = true;
        document.getElementById('income').value = 1;

        // إضافة الحقول المفقودة
        document.getElementById('chol_check_1').checked = true;
        document.getElementById('stroke_0').checked = true;
        document.getElementById('heart_disease_0').checked = true;
        document.getElementById('fruits_0').checked = true;
        document.getElementById('veggies_0').checked = true;
        document.getElementById('heavy_alcohol_0').checked = true;
        document.getElementById('any_healthcare_1').checked = true;
        document.getElementById('no_doc_cost_0').checked = true;
        document.getElementById('education').value = 1;
        document.querySelector('textarea[name="symptoms"]').value = 'صداع، دوخة، خفقان القلب، تعب';
        document.querySelector('textarea[name="medications"]').value = 'أدوية ضغط الدم';
        document.querySelector('textarea[name="family_history"]').value = 'تاريخ عائلي لأمراض القلب والضغط الدموي';

        // اختيار مرض ضغط الدم تلقائياً
        document.getElementById('disease_diabetes').checked = false;
        document.getElementById('disease_heart').checked = false;
        document.getElementById('disease_hypertension').checked = true;

        calculateBMI();

        // إرسال النموذج تلقائياً بعد ملء البيانات
        setTimeout(() => {
            document.getElementById('predictionForm').dispatchEvent(new Event('submit'));
        }, 500);
    }

    function fillExample6() {
        // اختيار مريض وطبيب افتراضي
        const patientSelect = document.getElementById('patient_id');
        const doctorSelect = document.getElementById('doctor_id');
        if (patientSelect.options.length > 1) patientSelect.selectedIndex = 1;
        if (doctorSelect.options.length > 1) doctorSelect.selectedIndex = 1;

        document.getElementById('weight').value = 78;
        document.getElementById('height').value = 162;
        document.getElementById('blood_pressure_systolic').value = 128;
        document.getElementById('blood_pressure_diastolic').value = 85;
        document.getElementById('blood_sugar_fasting').value = 92;
        document.getElementById('blood_sugar_random').value = 115;
        document.getElementById('high_bp_0').checked = true;
        document.getElementById('high_chol_0').checked = true;
        document.getElementById('smoker_0').checked = true;
        document.getElementById('phys_activity_1').checked = true;
        document.getElementById('fruits_1').checked = true;
        document.getElementById('veggies_1').checked = true;
        document.getElementById('gen_health').value = 2;
        document.getElementById('ment_health').value = 3;
        document.getElementById('phys_health').value = 2;
        document.getElementById('diff_walking_0').checked = true;
        document.getElementById('income').value = 6;

        // إضافة الحقول المفقودة
        document.getElementById('chol_check_1').checked = true;
        document.getElementById('stroke_0').checked = true;
        document.getElementById('heart_disease_0').checked = true;
        document.getElementById('heavy_alcohol_0').checked = true;
        document.getElementById('any_healthcare_1').checked = true;
        document.getElementById('no_doc_cost_0').checked = true;
        document.getElementById('education').value = 5;
        document.querySelector('textarea[name="symptoms"]').value = 'غثيان، تعب خفيف، زيادة في الوزن، تكرار التبول';
        document.querySelector('textarea[name="medications"]').value = 'فيتامينات ما قبل الولادة، حمض الفوليك';
        document.querySelector('textarea[name="family_history"]').value = 'الأم مصابة بالسكري، تاريخ ولادة قيصرية';

        // اختيار مرض السكري (سيدة حامل معرضة لخطر السكري الحملي)
        document.getElementById('disease_diabetes').checked = true;
        document.getElementById('disease_heart').checked = false;
        document.getElementById('disease_hypertension').checked = false;

        calculateBMI();

        setTimeout(() => {
            document.getElementById('predictionForm').dispatchEvent(new Event('submit'));
        }, 500);
    }

    function fillExample4() {
        // شخص سليم
        // اختيار مريض وطبيب افتراضي
        const patientSelect = document.getElementById('patient_id');
        const doctorSelect = document.getElementById('doctor_id');
        if (patientSelect.options.length > 1) patientSelect.selectedIndex = 1;
        if (doctorSelect.options.length > 1) doctorSelect.selectedIndex = 1;

        document.getElementById('weight').value = 70;
        document.getElementById('height').value = 175;
        document.getElementById('blood_pressure_systolic').value = 120;
        document.getElementById('blood_pressure_diastolic').value = 80;
        document.getElementById('blood_sugar_fasting').value = 85;
        document.getElementById('blood_sugar_random').value = 100;
        document.getElementById('high_bp_0').checked = true;
        document.getElementById('high_chol_0').checked = true;
        document.getElementById('smoker_0').checked = true;
        document.getElementById('phys_activity_1').checked = true;
        document.getElementById('fruits_1').checked = true;
        document.getElementById('veggies_1').checked = true;
        document.getElementById('gen_health').value = 1;
        document.getElementById('ment_health').value = 0;
        document.getElementById('phys_health').value = 0;
        document.getElementById('diff_walking_0').checked = true;
        document.getElementById('income').value = 7;

        // إضافة الحقول المفقودة
        document.getElementById('chol_check_1').checked = true;
        document.getElementById('stroke_0').checked = true;
        document.getElementById('heart_disease_0').checked = true;
        document.getElementById('heavy_alcohol_0').checked = true;
        document.getElementById('any_healthcare_1').checked = true;
        document.getElementById('no_doc_cost_0').checked = true;
        document.getElementById('education').value = 6;
        document.querySelector('textarea[name="symptoms"]').value = 'لا توجد أعراض، حالة صحية ممتازة';
        document.querySelector('textarea[name="medications"]').value = 'لا يوجد أدوية';
        document.querySelector('textarea[name="family_history"]').value = 'لا يوجد تاريخ مرضي في العائلة';

        // اختيار كل الأمراض للاختبار (شخص سليم يجب يظهر نتيجة سلبية)
        document.getElementById('disease_diabetes').checked = true;
        document.getElementById('disease_heart').checked = true;
        document.getElementById('disease_hypertension').checked = true;

        calculateBMI();

        // إرسال النموذج تلقائياً بعد ملء البيانات
        setTimeout(() => {
            document.getElementById('predictionForm').dispatchEvent(new Event('submit'));
        }, 500);
    }

    function fillExample5() {
        // مريض متعدد الأمراض
        // اختيار مريض وطبيب افتراضي
        const patientSelect = document.getElementById('patient_id');
        const doctorSelect = document.getElementById('doctor_id');
        if (patientSelect.options.length > 1) patientSelect.selectedIndex = 1;
        if (doctorSelect.options.length > 1) doctorSelect.selectedIndex = 1;

        document.getElementById('weight').value = 105;
        document.getElementById('height').value = 165;
        document.getElementById('blood_pressure_systolic').value = 170;
        document.getElementById('blood_pressure_diastolic').value = 100;
        document.getElementById('blood_sugar_fasting').value = 180;
        document.getElementById('blood_sugar_random').value = 220;
        document.getElementById('high_bp_1').checked = true;
        document.getElementById('high_chol_1').checked = true;
        document.getElementById('smoker_1').checked = true;
        document.getElementById('stroke_1').checked = true;
        document.getElementById('heart_disease_1').checked = true;
        document.getElementById('phys_activity_0').checked = true;
        document.getElementById('fruits_0').checked = true;
        document.getElementById('veggies_0').checked = true;
        document.getElementById('heavy_alcohol_1').checked = true;
        document.getElementById('gen_health').value = 5;
        document.getElementById('ment_health').value = 25;
        document.getElementById('phys_health').value = 30;
        document.getElementById('diff_walking_1').checked = true;
        document.getElementById('income').value = 2;

        // إضافة الحقول المفقودة
        document.getElementById('chol_check_1').checked = true;
        document.getElementById('any_healthcare_0').checked = true;
        document.getElementById('no_doc_cost_1').checked = true;
        document.getElementById('education').value = 1;
        document.querySelector('textarea[name="symptoms"]').value = 'تعب شديد، ألم في الصدر، ضيق في التنفس، دوخة، خفقان، عطش، كثرة تبول، تورم في الساقين';
        document.querySelector('textarea[name="medications"]').value = 'أدوية ضغط الدم، أدوية السكري، أدوية الكوليسترول، مسكنات';
        document.querySelector('textarea[name="family_history"]').value = 'تاريخ عائلي قوي لكل الأمراض: السكري، أمراض القلب، ضغط الدم، سكتات دماغية';

        // اختيار كل الأمراض (مريض متعدد الأمراض)
        document.getElementById('disease_diabetes').checked = true;
        document.getElementById('disease_heart').checked = true;
        document.getElementById('disease_hypertension').checked = true;

        calculateBMI();

        // إرسال النموذج تلقائياً بعد ملء البيانات
        setTimeout(() => {
            document.getElementById('predictionForm').dispatchEvent(new Event('submit'));
        }, 500);
    }

    // إرسال النموذج
    document.getElementById('predictionForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submitBtn');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const formData = new FormData(this);

        // التحقق من اختيار مرض واحد على الأقل
        const diseases = formData.getAll('diseases[]');
        if (diseases.length === 0) {
            alert('يرجى اختيار مرض واحد على الأقل للتنبؤ');
            return;
        }

        // تحويل البيانات إلى JSON
        const data = {};
        for (let [key, value] of formData.entries()) {
            if (key === 'diseases[]') {
                if (!data.diseases) data.diseases = [];
                data.diseases.push(value);
            } else if (value) {
                data[key] = value;
            }
        }

        // تحويل القيم الرقمية
        ['weight', 'height', 'blood_pressure_systolic', 'blood_pressure_diastolic',
            'blood_sugar_fasting', 'blood_sugar_random', 'bmi'
        ].forEach(field => {
            if (data[field]) data[field] = parseFloat(data[field]);
        });

        // تحويل القيم المنطقية
        ['high_bp', 'high_chol', 'chol_check', 'smoker', 'stroke', 'heart_disease',
            'phys_activity', 'fruits', 'veggies', 'heavy_alcohol', 'any_healthcare',
            'no_doc_cost', 'diff_walking'
        ].forEach(field => {
            data[field] = parseInt(data[field]) || 0;
        });

        submitBtn.disabled = true;
        loadingIndicator.style.display = 'block';

        try {
            const response = await fetch('{{ route("ai.predict") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                displayResults(result.results, result.encounter_id);
            } else {
                alert('حدث خطأ: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('فشل في الاتصال بخدمة التنبؤ');
        } finally {
            submitBtn.disabled = false;
            loadingIndicator.style.display = 'none';
        }
    });

    // عرض نتائج التنبؤ
    function displayResults(results, encounterId) {
        const resultsContent = document.getElementById('resultsContent');
        const viewDetailsBtn = document.getElementById('viewDetailsBtn');

        let html = '<div class="row">';

        results.forEach(result => {
            const riskColor =
                result.risk_level === 'high' ? 'danger' :
                result.risk_level === 'medium' ? 'warning' : 'success';

            const riskIcon =
                result.risk_level === 'high' ? '⚠️' :
                result.risk_level === 'medium' ? '⚡' : '✅';

            html += `
            <div class="col-md-6 mb-3">
                <div class="card border-${riskColor}">
                    <div class="card-header bg-${riskColor} text-white">
                        <h6 class="mb-0">${riskIcon} ${getDiseaseName(result.disease_type)}</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <strong>النتيجة:</strong> 
                            <span class="badge bg-${result.prediction === 1 ? 'danger' : 'success'}">
                                ${result.prediction === 1 ? 'موجود' : 'غير موجود'}
                            </span>
                        </p>
                        <p class="mb-2">
                            <strong>الاحتمالية:</strong> ${(result.probability * 100).toFixed(1)}%
                        </p>
                        <p class="mb-2">
                            <strong>مستوى الخطر:</strong> 
                            <span class="badge bg-${riskColor}">
                                ${getRiskLevelName(result.risk_level)}
                            </span>
                        </p>
                        <p class="mb-0">
                            <strong>الثقة:</strong> ${(result.confidence_score * 100).toFixed(1)}%
                        </p>
                    </div>
                </div>
            </div>
        `;
        });

        html += '</div>';

        // عرض التوصيات
        const hasRecommendations = results.some(
            r => r.recommendations && r.recommendations.length > 0
        );

        if (hasRecommendations) {
            html += '<hr><h6>📋 التوصيات الطبية:</h6><div class="row">';

            results.forEach(result => {
                if (result.recommendations && result.recommendations.length > 0) {
                    html += `
                    <div class="col-md-4">
                        <h6>${getDiseaseName(result.disease_type)}</h6>
                        <ul class="list-unstyled">
                `;

                    result.recommendations.forEach(rec => {
                        html += `<li><i class="fas fa-check text-success"></i> ${rec}</li>`;
                    });

                    html += '</ul></div>';
                }
            });

            html += '</div>';
        }

        resultsContent.innerHTML = html;

        // زر التفاصيل
        viewDetailsBtn.href = `{{ route('ai.show', ':encounterId') }}`
            .replace(':encounterId', encounterId);

        const modal = new bootstrap.Modal(document.getElementById('resultsModal'));
        modal.show();
    }

    function getDiseaseName(disease) {
        const names = {
            'diabetes': '<i class="fas fa-tint"></i> السكري',
            'heart_disease': '<i class="fas fa-heart"></i> أمراض القلب',
            'hypertension': '<i class="fas fa-tachometer-alt"></i> ضغط الدم'
        };
        return names[disease] || disease;
    }

    function getRiskLevelName(level) {
        const names = {
            'low': 'منخفض',
            'medium': 'متوسط',
            'high': 'عالي'
        };
        return names[level] || level;
    }
</script>
@endsection