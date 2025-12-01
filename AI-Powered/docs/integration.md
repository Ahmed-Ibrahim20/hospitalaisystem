# دليل الدمج مع SHMS (Laravel)

دليل شامل لدمج نظام التنبؤ بالسكري مع Smart Hospital Management System

## 📋 نظرة عامة

هذا الدليل يشرح كيفية دمج API التنبؤ بالسكري مع نظام SHMS المبني على Laravel.

## 🔧 المتطلبات الأساسية

### 1. متطلبات Python (API Server)
```bash
pip install -r requirements.txt
```

### 2. متطلبات Laravel (SHMS)
```bash
composer require guzzlehttp/guzzle
composer require firebase/php-jwt
```

## 🚀 خطوات التشغيل

### الخطوة 1: تدريب النموذج

```bash
cd models
python baseline_diabetes.py
```

**النتيجة المتوقعة:**
- ✅ ملف `models/saved/diabetes_model.pkl`
- ✅ ملف `models/saved/diabetes_model_metadata.json`

### الخطوة 2: تشغيل API Server

```bash
cd deployment
python fastapi_service.py
```

أو باستخدام uvicorn مباشرة:
```bash
uvicorn fastapi_service:app --host 0.0.0.0 --port 8000 --reload
```

**التحقق من التشغيل:**
```bash
curl http://localhost:8000/health
```

### الخطوة 3: اختبار API

```bash
curl -X POST "http://localhost:8000/predict" \
  -H "Content-Type: application/json" \
  -d '{
    "HighBP": 1,
    "HighChol": 1,
    "CholCheck": 1,
    "BMI": 28.5,
    "Smoker": 0,
    "Stroke": 0,
    "HeartDiseaseorAttack": 0,
    "PhysActivity": 1,
    "Fruits": 1,
    "Veggies": 1,
    "HvyAlcoholConsump": 0,
    "AnyHealthcare": 1,
    "NoDocbcCost": 0,
    "GenHlth": 3,
    "MentHlth": 5,
    "PhysHlth": 10,
    "DiffWalk": 0,
    "Sex": 1,
    "Age": 9,
    "Education": 4,
    "Income": 6
  }'
```

## 🔗 الدمج مع Laravel

### 1. إنشاء Service Class

أنشئ ملف `app/Services/DiabetesPredictionService.php`:

```php
<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DiabetesPredictionService
{
    protected $client;
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = config('services.diabetes_api.url', 'http://localhost:8000');
        $this->token = config('services.diabetes_api.token', 'demo_token_12345');
        
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30.0,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ]
        ]);
    }

    /**
     * التنبؤ بخطر السكري لمريض واحد
     *
     * @param array $patientData
     * @return array|null
     */
    public function predict(array $patientData): ?array
    {
        try {
            $response = $this->client->post('/predict', [
                'json' => $patientData
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            
            // حفظ في السجل
            Log::info('Diabetes prediction completed', [
                'patient_id' => $patientData['patient_id'] ?? null,
                'prediction' => $result['prediction'] ?? null,
                'probability' => $result['probability'] ?? null
            ]);

            return $result;

        } catch (GuzzleException $e) {
            Log::error('Diabetes prediction failed', [
                'error' => $e->getMessage(),
                'patient_data' => $patientData
            ]);
            
            return null;
        }
    }

    /**
     * التنبؤ لعدة مرضى دفعة واحدة
     *
     * @param array $patientsData
     * @return array|null
     */
    public function predictBatch(array $patientsData): ?array
    {
        try {
            $response = $this->client->post('/predict/batch', [
                'json' => $patientsData
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (GuzzleException $e) {
            Log::error('Batch prediction failed', [
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * فحص حالة API
     *
     * @return bool
     */
    public function checkHealth(): bool
    {
        try {
            $response = $this->client->get('/health');
            $health = json_decode($response->getBody()->getContents(), true);
            
            return $health['model_loaded'] ?? false;

        } catch (GuzzleException $e) {
            return false;
        }
    }

    /**
     * الحصول على معلومات النموذج
     *
     * @return array|null
     */
    public function getModelInfo(): ?array
    {
        // استخدام cache لتقليل الطلبات
        return Cache::remember('diabetes_model_info', 3600, function () {
            try {
                $response = $this->client->get('/model/info');
                return json_decode($response->getBody()->getContents(), true);
            } catch (GuzzleException $e) {
                return null;
            }
        });
    }
}
```

### 2. إضافة Configuration

أضف في `config/services.php`:

```php
'diabetes_api' => [
    'url' => env('DIABETES_API_URL', 'http://localhost:8000'),
    'token' => env('DIABETES_API_TOKEN', 'demo_token_12345'),
],
```

وفي `.env`:

```env
DIABETES_API_URL=http://localhost:8000
DIABETES_API_TOKEN=your_secure_token_here
```

### 3. إنشاء Controller

أنشئ `app/Http/Controllers/DiabetesPredictionController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Services\DiabetesPredictionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DiabetesPredictionController extends Controller
{
    protected $predictionService;

    public function __construct(DiabetesPredictionService $predictionService)
    {
        $this->predictionService = $predictionService;
    }

    /**
     * عرض صفحة التنبؤ
     */
    public function index()
    {
        return view('diabetes.predict');
    }

    /**
     * التنبؤ بخطر السكري
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function predict(Request $request): JsonResponse
    {
        // التحقق من البيانات
        $validated = $request->validate([
            'patient_id' => 'required|integer',
            'HighBP' => 'required|integer|between:0,1',
            'HighChol' => 'required|integer|between:0,1',
            'CholCheck' => 'required|integer|between:0,1',
            'BMI' => 'required|numeric|between:10,100',
            'Smoker' => 'required|integer|between:0,1',
            'Stroke' => 'required|integer|between:0,1',
            'HeartDiseaseorAttack' => 'required|integer|between:0,1',
            'PhysActivity' => 'required|integer|between:0,1',
            'Fruits' => 'required|integer|between:0,1',
            'Veggies' => 'required|integer|between:0,1',
            'HvyAlcoholConsump' => 'required|integer|between:0,1',
            'AnyHealthcare' => 'required|integer|between:0,1',
            'NoDocbcCost' => 'required|integer|between:0,1',
            'GenHlth' => 'required|integer|between:1,5',
            'MentHlth' => 'required|numeric|between:0,30',
            'PhysHlth' => 'required|numeric|between:0,30',
            'DiffWalk' => 'required|integer|between:0,1',
            'Sex' => 'required|integer|between:0,1',
            'Age' => 'required|integer|between:1,13',
            'Education' => 'required|integer|between:1,6',
            'Income' => 'required|integer|between:1,8',
        ]);

        // استدعاء API
        $result = $this->predictionService->predict($validated);

        if ($result === null) {
            return response()->json([
                'success' => false,
                'message' => 'فشل الاتصال بخدمة التنبؤ'
            ], 500);
        }

        // حفظ النتيجة في قاعدة البيانات (اختياري)
        // DiabetesPrediction::create([...]);

        return response()->json($result);
    }

    /**
     * فحص حالة الخدمة
     */
    public function health(): JsonResponse
    {
        $isHealthy = $this->predictionService->checkHealth();
        
        return response()->json([
            'status' => $isHealthy ? 'healthy' : 'unavailable',
            'model_loaded' => $isHealthy
        ]);
    }
}
```

### 4. إضافة Routes

في `routes/web.php`:

```php
use App\Http\Controllers\DiabetesPredictionController;

Route::prefix('diabetes')->group(function () {
    Route::get('/predict', [DiabetesPredictionController::class, 'index'])
        ->name('diabetes.predict');
    Route::post('/predict', [DiabetesPredictionController::class, 'predict'])
        ->name('diabetes.predict.submit');
    Route::get('/health', [DiabetesPredictionController::class, 'health'])
        ->name('diabetes.health');
});
```

### 5. إنشاء View (Blade Template)

أنشئ `resources/views/diabetes/predict.blade.php`:

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h3>🏥 التنبؤ بخطر الإصابة بالسكري</h3>
                </div>

                <div class="card-body">
                    <form id="diabetesPredictionForm">
                        @csrf
                        
                        <input type="hidden" name="patient_id" value="{{ $patient->id ?? 1 }}">

                        <!-- معلومات القياسات الحيوية -->
                        <h5 class="mb-3">📊 القياسات الحيوية</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>ضغط الدم المرتفع</label>
                                <select name="HighBP" class="form-control" required>
                                    <option value="0">لا</option>
                                    <option value="1">نعم</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>الكوليسترول العالي</label>
                                <select name="HighChol" class="form-control" required>
                                    <option value="0">لا</option>
                                    <option value="1">نعم</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label>مؤشر كتلة الجسم (BMI)</label>
                                <input type="number" name="BMI" class="form-control" 
                                       step="0.1" min="10" max="100" required>
                            </div>
                        </div>

                        <!-- يمكن إضافة باقي الحقول بنفس الطريقة -->

                        <button type="submit" class="btn btn-primary btn-lg mt-3">
                            🔍 التنبؤ بالخطر
                        </button>
                    </form>

                    <!-- عرض النتائج -->
                    <div id="predictionResults" class="mt-4" style="display: none;">
                        <div class="alert" id="resultAlert">
                            <h4 id="resultTitle"></h4>
                            <p id="resultProbability"></p>
                            <div id="resultRecommendations"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('diabetesPredictionForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    // تحويل القيم إلى أرقام
    for (let key in data) {
        if (key !== '_token') {
            data[key] = parseFloat(data[key]);
        }
    }
    
    try {
        const response = await fetch('{{ route("diabetes.predict.submit") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            displayResults(result);
        } else {
            alert('حدث خطأ في التنبؤ');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('فشل الاتصال بالخدمة');
    }
});

function displayResults(result) {
    const resultsDiv = document.getElementById('predictionResults');
    const alert = document.getElementById('resultAlert');
    const title = document.getElementById('resultTitle');
    const probability = document.getElementById('resultProbability');
    const recommendations = document.getElementById('resultRecommendations');
    
    // تحديد لون التنبيه
    alert.className = 'alert ' + (result.risk_level === 'عالي' ? 'alert-danger' : 
                                   result.risk_level === 'متوسط' ? 'alert-warning' : 
                                   'alert-success');
    
    title.textContent = result.prediction === 1 ? 
        '⚠️ يوجد خطر للإصابة بالسكري' : 
        '✅ خطر منخفض للإصابة بالسكري';
    
    probability.innerHTML = `
        <strong>الاحتمالية:</strong> ${(result.probability * 100).toFixed(1)}%<br>
        <strong>مستوى الخطر:</strong> ${result.risk_level}<br>
        <strong>الثقة:</strong> ${(result.confidence * 100).toFixed(1)}%
    `;
    
    recommendations.innerHTML = '<h5 class="mt-3">📋 التوصيات:</h5><ul>' +
        result.recommendations.map(rec => `<li>${rec}</li>`).join('') +
        '</ul>';
    
    resultsDiv.style.display = 'block';
}
</script>
@endsection
```

## 🔒 الأمان والخصوصية

### 1. استخدام JWT للمصادقة

في FastAPI (`deployment/fastapi_service.py`):

```python
from jose import JWTError, jwt
from datetime import datetime, timedelta

SECRET_KEY = "your-secret-key-here"  # استخدم متغير بيئة
ALGORITHM = "HS256"

def create_access_token(data: dict):
    to_encode = data.copy()
    expire = datetime.utcnow() + timedelta(hours=24)
    to_encode.update({"exp": expire})
    return jwt.encode(to_encode, SECRET_KEY, algorithm=ALGORITHM)

def verify_token(token: str):
    try:
        payload = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
        return payload
    except JWTError:
        raise HTTPException(status_code=401, detail="Invalid token")
```

### 2. تشفير البيانات

- استخدم HTTPS في الإنتاج
- لا تحفظ بيانات حساسة في logs
- استخدم environment variables للـ secrets

### 3. Rate Limiting

في Laravel (`app/Http/Kernel.php`):

```php
'api' => [
    'throttle:60,1',  // 60 طلب في الدقيقة
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

## 📊 المراقبة والصيانة

### 1. Logging

```php
// في Laravel
Log::channel('diabetes_predictions')->info('Prediction made', [
    'patient_id' => $patientId,
    'result' => $result
]);
```

### 2. Monitoring

- راقب وقت الاستجابة
- راقب معدل النجاح/الفشل
- راقب استخدام الذاكرة والـ CPU

### 3. Backup

- احفظ نسخة احتياطية من النموذج المدرب
- احفظ سجلات التنبؤات

## 🚨 استكشاف الأخطاء

### المشكلة: API لا يستجيب

```bash
# تحقق من تشغيل الخدمة
curl http://localhost:8000/health

# تحقق من logs
tail -f deployment/api.log
```

### المشكلة: خطأ في التنبؤ

- تحقق من صحة البيانات المدخلة
- تحقق من تحميل النموذج
- راجع logs الخاصة بـ FastAPI

## 📞 الدعم

للمساعدة أو الإبلاغ عن مشاكل، يرجى فتح Issue في المشروع.

---

**تم التحديث:** أكتوبر 2025
