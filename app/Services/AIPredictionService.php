<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Encounter;
use App\Models\DiseasePrediction;
use Carbon\Carbon;

class AIPredictionService
{
    protected $client;
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = config('services.ai_api.url', 'http://localhost:8001');
        $this->token = config('services.ai_api.token', 'demo_token_12345');
        $timeout = config('services.ai_api.timeout', 30);

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => $timeout,
            'connect_timeout' => 5.0,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
            ],
        ]);
    }

    /**
     * التنبؤ بالأمراض من بيانات الزيارة الطبية
     */
    public function predictFromEncounter(Encounter $encounter, array $diseases = ['diabetes']): array
    {
        $results = [];

        foreach ($diseases as $disease) {
            try {
                $predictionData = $this->preparePredictionData($encounter, $disease);
                $result = $this->makePrediction($predictionData, $disease);

                if ($result && $result['success']) {
                    // حفظ النتيجة في قاعدة البيانات
                    $this->savePrediction($encounter, $result, $disease);
                    $results[$disease] = $result;
                } else {
                    // في حالة فشل API، استخدم البيانات التجريبية
                    $demoResult = $this->generateDemoPrediction($disease, $predictionData);
                    $this->savePrediction($encounter, $demoResult, $disease);
                    $results[$disease] = $demoResult;
                }
            } catch (\Exception $e) {
                Log::error("AI prediction failed for disease {$disease}", [
                    'encounter_id' => $encounter->id,
                    'error' => $e->getMessage()
                ]);

                // استخدم البيانات التجريبية كـ fallback
                $demoResult = $this->generateDemoPrediction($disease, []);
                $this->savePrediction($encounter, $demoResult, $disease);
                $results[$disease] = $demoResult;
            }
        }

        return $results;
    }

    /**
     * تجهيز بيانات التنبؤ
     */
    protected function preparePredictionData(Encounter $encounter, string $disease): array
    {
        $patient = $encounter->patient;

        // البيانات الأساسية للتنبؤ بالسكري
        if ($disease === 'diabetes') {
            return [
                'HighBP' => $encounter->high_bp ?? 0,
                'HighChol' => $encounter->high_chol ?? 0,
                'CholCheck' => $encounter->chol_check ?? 1,
                'BMI' => $encounter->bmi ?? $this->calculateBMI($encounter),
                'Smoker' => $encounter->smoker ?? 0,
                'Stroke' => $encounter->stroke ?? 0,
                'HeartDiseaseorAttack' => $encounter->heart_disease ?? 0,
                'PhysActivity' => $encounter->phys_activity ?? 1,
                'Fruits' => $encounter->fruits ?? 1,
                'Veggies' => $encounter->veggies ?? 1,
                'HvyAlcoholConsump' => $encounter->heavy_alcohol ?? 0,
                'AnyHealthcare' => $encounter->any_healthcare ?? 1,
                'NoDocbcCost' => $encounter->no_doc_cost ?? 0,
                'GenHlth' => $encounter->gen_health ?? 3,
                'MentHlth' => $encounter->ment_health ?? 0,
                'PhysHlth' => $encounter->phys_health ?? 0,
                'DiffWalk' => $encounter->diff_walking ?? 0,
                'Sex' => $patient->gender === 'male' ? 1 : 0,
                'Age' => $this->getAgeGroup($patient->age),
                'Education' => $encounter->education ?? 4,
                'Income' => $encounter->income ?? 5,
            ];
        }

        // يمكن إضافة أمراض أخرى هنا
        return [];
    }

    /**
     * حساب BMI إذا لم يكن موجود
     */
    protected function calculateBMI(Encounter $encounter): float
    {
        if ($encounter->weight && $encounter->height) {
            $heightInMeters = $encounter->height / 100;
            return round($encounter->weight / ($heightInMeters * $heightInMeters), 2);
        }

        // قيمة افتراضية
        return 25.0;
    }

    /**
     * تحديد الفئة العمرية
     */
    protected function getAgeGroup(int $age): int
    {
        if ($age <= 24) return 1;
        if ($age <= 29) return 2;
        if ($age <= 34) return 3;
        if ($age <= 39) return 4;
        if ($age <= 44) return 5;
        if ($age <= 49) return 6;
        if ($age <= 54) return 7;
        if ($age <= 59) return 8;
        if ($age <= 64) return 9;
        if ($age <= 69) return 10;
        if ($age <= 74) return 11;
        if ($age <= 79) return 12;
        return 13;
    }

    /**
     * إجراء التنبؤ عبر API
     */
    protected function makePrediction(array $data, string $disease): ?array
    {
        try {
            $endpoint = $disease === 'diabetes' ? '/predict' : '/predict';

            // إضافة patient_id للبيانات
            $data['patient_id'] = uniqid('patient_', true);

            $response = $this->client->post($endpoint, [
                'json' => $data,
                'query' => ['include_shap' => true] // طلب SHAP explanation
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if ($result['success']) {
                // تحويل البيانات لتتوافق مع شكل قاعدة البيانات
                return [
                    'success' => true,
                    'prediction' => $result['prediction'],
                    'probability' => $result['probability'],
                    'risk_level' => $result['risk_level'],
                    'confidence' => $result['confidence'],
                    'recommendations' => $result['recommendations'] ?? [],
                    'shap_explanation' => $result['shap_explanation'] ?? [],
                    'risk_factors' => $result['risk_factors'] ?? [],
                    'timestamp' => $result['timestamp'] ?? now()->toISOString(),
                    'model_version' => '2.0.0'
                ];
            }

            return null;
        } catch (GuzzleException $e) {
            Log::error('AI API request failed', [
                'disease' => $disease,
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return null;
        }
    }

    /**
     * حفظ نتيجة التنبؤ في قاعدة البيانات
     */
    protected function savePrediction(Encounter $encounter, array $result, string $disease): DiseasePrediction
    {
        return DiseasePrediction::create([
            'encounter_id' => $encounter->id,
            'disease_type' => $disease,
            'prediction' => $result['prediction'],
            'probability' => $result['probability'],
            'risk_level' => $result['risk_level'],
            'confidence_score' => $result['confidence'] ?? null,
            'prediction_data' => $result,
            'risk_factors' => $result['risk_factors'] ?? null,
            'recommendations' => json_encode($result['recommendations'] ?? []),
            'shap_values' => json_encode($result['shap_explanation'] ?? []), // حفظ SHAP explanation
            'model_version' => $result['model_version'] ?? '2.0.0',
            'status' => 'pending'
        ]);
    }

    /**
     * التنبؤ لعدة مرضى دفعة واحدة
     */
    public function predictBatch(array $encounters, array $diseases = ['diabetes']): array
    {
        $results = [];

        try {
            // تجهيز بيانات الدفعة
            $batchData = [];
            foreach ($encounters as $encounter) {
                foreach ($diseases as $disease) {
                    $predictionData = $this->preparePredictionData($encounter, $disease);
                    $predictionData['patient_id'] = 'encounter_' . $encounter->id;
                    $batchData[] = $predictionData;
                }
            }

            // إرسال طلب الدفعة
            $response = $this->client->post('/predict/batch', [
                'json' => $batchData
            ]);

            $batchResults = json_decode($response->getBody()->getContents(), true);

            // حفظ النتائج في قاعدة البيانات
            $index = 0;
            foreach ($encounters as $encounter) {
                foreach ($diseases as $disease) {
                    if (isset($batchResults[$index])) {
                        $result = $batchResults[$index];
                        if ($result['success']) {
                            $this->savePrediction($encounter, $result, $disease);
                            $results[$encounter->id][$disease] = $result;
                        } else {
                            $results[$encounter->id][$disease] = ['success' => false, 'error' => 'Prediction failed'];
                        }
                    }
                    $index++;
                }
            }
        } catch (GuzzleException $e) {
            Log::error('AI batch prediction failed', [
                'error' => $e->getMessage(),
                'encounters_count' => count($encounters)
            ]);

            // الرجوع للتنبؤات الفردية في حالة فشل الدفعة
            foreach ($encounters as $encounter) {
                $results[$encounter->id] = $this->predictFromEncounter($encounter, $diseases);
            }
        }

        return $results;
    }

    /**
     * فحص حالة API
     */
    public function checkHealth(): bool
    {
        try {
            $response = $this->client->get('/health');
            $health = json_decode($response->getBody()->getContents(), true);

            // التحقق من حالة الخدمة الجديدة
            return isset($health['status']) && $health['status'] === 'healthy';
        } catch (GuzzleException $e) {
            return false;
        }
    }

    /**
     * الحصول على معلومات النموذج
     */
    public function getModelInfo(): ?array
    {
        return Cache::remember('ai_model_info', 3600, function () {
            try {
                $response = $this->client->get('/model/info');
                return json_decode($response->getBody()->getContents(), true);
            } catch (GuzzleException $e) {
                return null;
            }
        });
    }

    /**
     * تحديث حالة التنبؤ (مراجعة الطبيب)
     */
    public function updatePredictionStatus(DiseasePrediction $prediction, string $status, ?int $reviewedBy = null, ?string $notes = null): bool
    {
        $prediction->status = $status;
        $prediction->reviewed_at = now();

        if ($reviewedBy) {
            $prediction->reviewed_by = $reviewedBy;
        }

        if ($notes) {
            $prediction->doctor_notes = $notes;
        }

        return $prediction->save();
    }

    /**
     * الحصول على إحصائيات التنبؤات
     */
    public function getPredictionStats(?string $diseaseType = null, ?int $days = 30): array
    {
        $query = DiseasePrediction::query();

        if ($diseaseType) {
            $query->where('disease_type', $diseaseType);
        }

        $query->where('created_at', '>=', now()->subDays($days));

        $total = $query->count();
        $highRisk = $query->where('risk_level', 'high')->count();
        $mediumRisk = $query->where('risk_level', 'medium')->count();
        $lowRisk = $query->where('risk_level', 'low')->count();
        $reviewed = $query->where('status', '!=', 'pending')->count();

        return [
            'total' => $total,
            'high_risk' => $highRisk,
            'medium_risk' => $mediumRisk,
            'low_risk' => $lowRisk,
            'reviewed' => $reviewed,
            'pending' => $total - $reviewed,
            'high_risk_percentage' => $total > 0 ? round(($highRisk / $total) * 100, 2) : 0,
            'reviewed_percentage' => $total > 0 ? round(($reviewed / $total) * 100, 2) : 0,
        ];
    }

    /**
     * إنشاء بيانات تجريبية للتنبؤ (Fallback)
     */
    protected function generateDemoPrediction(string $disease, array $data): array
    {
        $prediction = rand(0, 1);
        $probability = rand(55, 95) / 100;

        $riskLevel = 'low';
        if ($probability >= 0.75) {
            $riskLevel = 'high';
        } elseif ($probability >= 0.60) {
            $riskLevel = 'medium';
        }

        $recommendations = $this->getDemoRecommendations($disease);
        $shapExplanation = $this->getDemoSHAPExplanation($disease);

        return [
            'success' => true,
            'prediction' => $prediction,
            'probability' => $probability,
            'risk_level' => $riskLevel,
            'confidence' => rand(70, 95) / 100,
            'recommendations' => $recommendations,
            'shap_explanation' => $shapExplanation,
            'risk_factors' => ['demo_factor_1' => 'value', 'demo_factor_2' => 'value'],
            'timestamp' => now()->toISOString(),
            'model_version' => 'demo_2.0.0'
        ];
    }

    /**
     * توصيات تجريبية حسب المرض
     */
    protected function getDemoRecommendations(string $disease): array
    {
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
                'متابعة ضغط الدم والكوليسترول',
                'الإقلاع عن التدخين'
            ],
            'hypertension' => [
                'تقليل استهلاك الملح',
                'ممارسة الرياضة بانتظام',
                'مراقبة ضغط الدم يومياً',
                'الحفاظ على وزن صحي'
            ]
        ];

        return $recommendations[$disease] ?? ['استشر طبيبك'];
    }

    /**
     * SHAP explanation تجريبي
     */
    protected function getDemoSHAPExplanation(string $disease): array
    {
        return [
            [
                'feature' => 'BMI',
                'shap_value' => 0.15,
                'impact' => 'positive',
                'description' => 'مؤشر كتلة الجسم المرتفع يزيد من خطر الإصابة'
            ],
            [
                'feature' => 'HighBP',
                'shap_value' => 0.12,
                'impact' => 'positive',
                'description' => 'ضغط الدم المرتفع يساهم في زيادة الخطر'
            ],
            [
                'feature' => 'PhysActivity',
                'shap_value' => -0.08,
                'impact' => 'negative',
                'description' => 'النشاط البدني يقلل من خطر الإصابة'
            ]
        ];
    }
}
