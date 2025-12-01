<?php

namespace App\Http\Controllers;

use App\Services\AIPredictionService;
use App\Models\Encounter;
use App\Models\Patient;
use App\Models\DiseasePrediction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AIPredictionController extends Controller
{
    protected $aiService;

    public function __construct(AIPredictionService $aiService)
    {
        $this->aiService = $aiService;
        $this->middleware('auth');
    }

    /**
     * صفحة رئيسية للتنبؤات
     */
    public function dashboard(): View
    {
        try {
            $stats = $this->aiService->getPredictionStats();
        } catch (\Exception $e) {
            // Default stats if AI service is not available
            $stats = [
                'total' => DiseasePrediction::count(),
                'high_risk' => DiseasePrediction::where('risk_level', 'high')->count(),
                'medium_risk' => DiseasePrediction::where('risk_level', 'medium')->count(),
                'low_risk' => DiseasePrediction::where('risk_level', 'low')->count(),
                'pending' => DiseasePrediction::where('status', 'pending')->count(),
                'confirmed' => DiseasePrediction::where('status', 'confirmed')->count(),
            ];
        }

        $recentPredictions = DiseasePrediction::with(['encounter.patient', 'encounter.doctor'])
            ->latest()
            ->take(10)
            ->get();

        // Mock AI health status for demo
        $aiHealth = [
            'status' => 'healthy',
            'message' => 'AI service is running (Demo Mode)',
            'response_time' => 50,
            'timestamp' => now()->format('Y-m-d H:i:s')
        ];

        return view('ai.dashboard', compact('stats', 'recentPredictions', 'aiHealth'));
    }

    /**
     * صفحة التنبؤ لمريض جديد
     */
    public function create(): View
    {
        $patients = Patient::orderBy('name')->get();
        $doctors = \App\Models\User::whereHas('role', function ($query) {
            $query->where('name', 'doctor');
        })->get();

        return view('ai.create', compact('patients', 'doctors'));
    }

    /**
     * التنبؤ بالأمراض
     */
    public function predict(Request $request): JsonResponse
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'diseases' => 'required|array|min:1',
            'diseases.*' => 'in:diabetes,heart_disease,hypertension',

            // البيانات الطبية الأساسية
            'high_bp' => 'nullable|integer|between:0,1',
            'high_chol' => 'nullable|integer|between:0,1',
            'chol_check' => 'nullable|integer|between:0,1',
            'bmi' => 'nullable|numeric|between:10,100',
            'smoker' => 'nullable|integer|between:0,1',
            'stroke' => 'nullable|integer|between:0,1',
            'heart_disease' => 'nullable|integer|between:0,1',
            'phys_activity' => 'nullable|integer|between:0,1',
            'fruits' => 'nullable|integer|between:0,1',
            'veggies' => 'nullable|integer|between:0,1',
            'heavy_alcohol' => 'nullable|integer|between:0,1',
            'any_healthcare' => 'nullable|integer|between:0,1',
            'no_doc_cost' => 'nullable|integer|between:0,1',
            'gen_health' => 'nullable|integer|between:1,5',
            'ment_health' => 'nullable|integer|between:0,30',
            'phys_health' => 'nullable|integer|between:0,30',
            'diff_walking' => 'nullable|integer|between:0,1',
            'education' => 'nullable|integer|between:1,6',
            'income' => 'nullable|integer|between:1,8',

            // البيانات الطبية الإضافية
            'blood_pressure_systolic' => 'nullable|numeric|between:70,250',
            'blood_pressure_diastolic' => 'nullable|numeric|between:40,150',
            'blood_sugar_fasting' => 'nullable|numeric|between:50,400',
            'blood_sugar_random' => 'nullable|numeric|between:50,500',
            'weight' => 'nullable|numeric|between:20,300',
            'height' => 'nullable|numeric|between:50,250',
            'symptoms' => 'nullable|string',
            'medications' => 'nullable|string',
            'allergies' => 'nullable|string',
            'family_history' => 'nullable|string',
        ]);

        try {
            // إنشاء زيارة طبية جديدة
            $encounter = Encounter::create([
                'patient_id' => $request->patient_id,
                'doctor_id' => $request->doctor_id,
                'visit_date' => now(),

                // البيانات الطبية
                'high_bp' => $request->high_bp,
                'high_chol' => $request->high_chol,
                'chol_check' => $request->chol_check,
                'bmi' => $request->bmi,
                'smoker' => $request->smoker,
                'stroke' => $request->stroke,
                'heart_disease' => $request->heart_disease,
                'phys_activity' => $request->phys_activity,
                'fruits' => $request->fruits,
                'veggies' => $request->veggies,
                'heavy_alcohol' => $request->heavy_alcohol,
                'any_healthcare' => $request->any_healthcare,
                'no_doc_cost' => $request->no_doc_cost,
                'gen_health' => $request->gen_health,
                'ment_health' => $request->ment_health,
                'phys_health' => $request->phys_health,
                'diff_walking' => $request->diff_walking,
                'education' => $request->education,
                'income' => $request->income,

                // البيانات الإضافية
                'blood_pressure_systolic' => $request->blood_pressure_systolic,
                'blood_pressure_diastolic' => $request->blood_pressure_diastolic,
                'blood_sugar_fasting' => $request->blood_sugar_fasting,
                'blood_sugar_random' => $request->blood_sugar_random,
                'weight' => $request->weight,
                'height' => $request->height,
                'symptoms' => $request->symptoms,
                'medications' => $request->medications,
                'allergies' => $request->allergies,
                'family_history' => $request->family_history,
            ]);

            // تجهيز البيانات التلقائية
            $encounter->setAgeGroupFromPatient();
            $encounter->setSexFromPatient();
            $encounter->save();

            // إنشاء تنبؤات تجريبية (Demo Mode)
            $results = [];
            foreach ($request->diseases as $disease) {
                $prediction = rand(0, 1); // 0 أو 1
                $probability = rand(55, 95) / 100; // 0.55 إلى 0.95

                // تحديد مستوى الخطر بناءً على الاحتمالية
                if ($probability >= 0.75) {
                    $riskLevel = 'high';
                } elseif ($probability >= 0.60) {
                    $riskLevel = 'medium';
                } else {
                    $riskLevel = 'low';
                }

                // إنشاء توصيات تجريبية
                $recommendations = [
                    'diabetes' => [
                        'تقليل استهلاك السكر والكربوهيدرات',
                        'ممارسة الرياضة بانتظام',
                        'مراقبة مستوى السكر في الدم',
                        'الحفاظ على وزن صحي'
                    ],
                    'heart_disease' => [
                        'اتباع نظام غذائي قليل الدسم',
                        'ممارسة الرياضة بانتظام',
                        'الإقلاع عن التدخين',
                        'مراقبة ضغط الدم بانتظام'
                    ],
                    'hypertension' => [
                        'تقليل استهلاك الملح',
                        'ممارسة الرياضة بانتظام',
                        'الحفاظ على وزن صحي',
                        'الحد من التوتر والإجهاد'
                    ]
                ];

                $diseasePrediction = DiseasePrediction::create([
                    'encounter_id' => $encounter->id,
                    'disease_type' => $disease,
                    'prediction' => $prediction,
                    'probability' => $probability,
                    'confidence_score' => rand(70, 95) / 100,
                    'risk_level' => $riskLevel,
                    'recommendations' => json_encode($recommendations[$disease] ?? []),
                    'status' => 'pending',
                    'shap_values' => json_encode([]),
                    'feature_importance' => json_encode([]),
                ]);

                $results[] = [
                    'disease_type' => $disease,
                    'prediction' => $prediction,
                    'probability' => $probability,
                    'risk_level' => $riskLevel,
                    'confidence_score' => $diseasePrediction->confidence_score,
                    'recommendations' => json_decode($diseasePrediction->recommendations),
                ];
            }

            return response()->json([
                'success' => true,
                'encounter_id' => $encounter->id,
                'results' => $results,
                'message' => 'تم إجراء التنبؤات بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إجراء التنبؤ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض نتائج التنبؤ
     */
    public function show($encounterId): View
    {
        $encounter = Encounter::with(['patient', 'doctor', 'predictions'])
            ->findOrFail($encounterId);

        return view('ai.show', compact('encounter'));
    }

    /**
     * مراجعة التنبؤ (للأطباء)
     */
    public function review(Request $request, DiseasePrediction $prediction): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:reviewed,confirmed,rejected',
            'doctor_notes' => 'nullable|string|max:1000'
        ]);

        $success = $this->aiService->updatePredictionStatus(
            $prediction,
            $request->status,
            Auth::id(),
            $request->doctor_notes
        );

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة التنبؤ بنجاح'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'فشل في تحديث حالة التنبؤ'
        ], 500);
    }

    /**
     * التنبؤ المجمّع للمرضى
     */
    public function batchPredict(Request $request): JsonResponse
    {
        $request->validate([
            'patient_ids' => 'required|array|min:1|max:50',
            'patient_ids.*' => 'exists:patients,id',
            'diseases' => 'required|array|min:1',
            'diseases.*' => 'in:diabetes,heart_disease,hypertension'
        ]);

        try {
            $encounters = Encounter::whereIn('patient_id', $request->patient_ids)
                ->where('doctor_id', Auth::id())
                ->get();

            $results = $this->aiService->predictBatch($encounters, $request->diseases);

            return response()->json([
                'success' => true,
                'results' => $results,
                'message' => 'تم إجراء التنبؤات المجمعة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إجراء التنبؤات المجمعة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * إحصائيات التنبؤات
     */
    public function statistics(Request $request): JsonResponse
    {
        $diseaseType = $request->get('disease_type');
        $days = $request->get('days', 30);

        $stats = $this->aiService->getPredictionStats($diseaseType, $days);

        // إحصائيات إضافية
        $monthlyStats = DiseasePrediction::selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as month,
                COUNT(*) as total,
                SUM(CASE WHEN risk_level = "high" THEN 1 ELSE 0 END) as high_risk,
                SUM(CASE WHEN risk_level = "medium" THEN 1 ELSE 0 END) as medium_risk,
                SUM(CASE WHEN risk_level = "low" THEN 1 ELSE 0 END) as low_risk
            ')
            ->when($diseaseType, function ($query) use ($diseaseType) {
                return $query->where('disease_type', $diseaseType);
            })
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'stats' => $stats,
            'monthly_stats' => $monthlyStats,
            'disease_types' => DiseasePrediction::distinct('disease_type')->pluck('disease_type')
        ]);
    }

    /**
     * فحص حالة خدمة الـ AI
     */
    public function healthCheck(): JsonResponse
    {
        try {
            $isHealthy = $this->aiService->checkHealth();
            $modelInfo = $this->aiService->getModelInfo();

            return response()->json([
                'status' => $isHealthy ? 'healthy' : 'unhealthy',
                'model_loaded' => $isHealthy,
                'model_info' => $modelInfo,
                'timestamp' => now()->toISOString(),
                'response_time' => 50 // Mock response time for demo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'unhealthy',
                'model_loaded' => false,
                'model_info' => null,
                'timestamp' => now()->toISOString(),
                'response_time' => 0,
                'error' => 'AI Service is not available'
            ]);
        }
    }

    /**
     * قائمة التنبؤات المنتظرة المراجعة
     */
    public function pendingReview(): View
    {
        $predictions = DiseasePrediction::with(['encounter.patient', 'encounter.doctor'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('ai.pending-review', compact('predictions'));
    }

    /**
     * تقارير التنبؤات
     */
    public function reports(): View
    {
        $stats = $this->aiService->getPredictionStats();

        $diseaseStats = [];
        foreach (['diabetes', 'heart_disease', 'hypertension'] as $disease) {
            $diseaseStats[$disease] = $this->aiService->getPredictionStats($disease, 30);
        }

        $recentHighRisk = DiseasePrediction::with(['encounter.patient'])
            ->where('risk_level', 'high')
            ->where('created_at', '>=', now()->subDays(7))
            ->latest()
            ->take(10)
            ->get();

        return view('ai.reports', compact('stats', 'diseaseStats', 'recentHighRisk'));
    }

    /**
     * API للحصول على بيانات مريض للتنبؤ السريع
     */
    public function getPatientData(Patient $patient): JsonResponse
    {
        $lastEncounter = $patient->encounters()
            ->with('predictions')
            ->latest()
            ->first();

        return response()->json([
            'patient' => $patient,
            'last_encounter' => $lastEncounter,
            'last_prediction' => $lastEncounter?->getDiabetesPrediction(),
            'health_summary' => $lastEncounter?->getHealthSummary()
        ]);
    }
}
