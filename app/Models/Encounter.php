<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Encounter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'visit_date',
        // AI Medical Data
        'high_bp',
        'high_chol',
        'chol_check',
        'bmi',
        'smoker',
        'stroke',
        'heart_disease',
        'phys_activity',
        'fruits',
        'veggies',
        'heavy_alcohol',
        'any_healthcare',
        'no_doc_cost',
        'gen_health',
        'ment_health',
        'phys_health',
        'diff_walking',
        'sex',
        'age_group',
        'education',
        'income',
        // Additional Medical Data
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'blood_sugar_fasting',
        'blood_sugar_random',
        'weight',
        'height',
        'symptoms',
        'medications',
        'allergies',
        'family_history'
    ];

    protected $casts = [
        'visit_date' => 'datetime',
        'blood_pressure_systolic' => 'decimal:2',
        'blood_pressure_diastolic' => 'decimal:2',
        'blood_sugar_fasting' => 'decimal:2',
        'blood_sugar_random' => 'decimal:2',
        'weight' => 'decimal:2',
        'height' => 'decimal:2',
        'symptoms' => 'array',
        'medications' => 'array',
        'allergies' => 'array',
        'family_history' => 'array',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function predictions()
    {
        return $this->hasMany(DiseasePrediction::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    /**
     * الحصول على آخر تنبؤ للسكري
     */
    public function getDiabetesPrediction()
    {
        return $this->predictions()
            ->where('disease_type', 'diabetes')
            ->latest()
            ->first();
    }

    /**
     * التحقق إذا كان هناك تنبؤ عالي الخطورة
     */
    public function hasHighRiskPrediction(): bool
    {
        return $this->predictions()
            ->where('risk_level', 'high')
            ->exists();
    }

    /**
     * الحصول على جميع التنبؤات العالية الخطورة
     */
    public function getHighRiskPredictions()
    {
        return $this->predictions()
            ->where('risk_level', 'high')
            ->get();
    }

    /**
     * حساب BMI تلقائياً من الوزن والطول
     */
    public function calculateBmi(): float
    {
        if ($this->weight && $this->height) {
            $heightInMeters = $this->height / 100;
            return round($this->weight / ($heightInMeters * $heightInMeters), 2);
        }

        return $this->bmi ?? 25.0;
    }

    /**
     * تحديد فئة العمر من عمر المريض
     */
    public function setAgeGroupFromPatient()
    {
        if ($this->patient && $this->patient->age) {
            $age = $this->patient->age;

            if ($age <= 24) $this->age_group = 1;
            elseif ($age <= 29) $this->age_group = 2;
            elseif ($age <= 34) $this->age_group = 3;
            elseif ($age <= 39) $this->age_group = 4;
            elseif ($age <= 44) $this->age_group = 5;
            elseif ($age <= 49) $this->age_group = 6;
            elseif ($age <= 54) $this->age_group = 7;
            elseif ($age <= 59) $this->age_group = 8;
            elseif ($age <= 64) $this->age_group = 9;
            elseif ($age <= 69) $this->age_group = 10;
            elseif ($age <= 74) $this->age_group = 11;
            elseif ($age <= 79) $this->age_group = 12;
            else $this->age_group = 13;
        }
    }

    /**
     * تحديد الجنس من المريض
     */
    public function setSexFromPatient()
    {
        if ($this->patient && $this->patient->gender) {
            $this->sex = $this->patient->gender === 'male' ? 1 : 0;
        }
    }

    /**
     * تجهيز البيانات للتنبؤ التلقائي
     */
    public function prepareForPrediction(): array
    {
        $this->setAgeGroupFromPatient();
        $this->setSexFromPatient();

        if (!$this->bmi) {
            $this->bmi = $this->calculateBmi();
        }

        return [
            'HighBP' => $this->high_bp ?? 0,
            'HighChol' => $this->high_chol ?? 0,
            'CholCheck' => $this->chol_check ?? 1,
            'BMI' => $this->bmi,
            'Smoker' => $this->smoker ?? 0,
            'Stroke' => $this->stroke ?? 0,
            'HeartDiseaseorAttack' => $this->heart_disease ?? 0,
            'PhysActivity' => $this->phys_activity ?? 1,
            'Fruits' => $this->fruits ?? 1,
            'Veggies' => $this->veggies ?? 1,
            'HvyAlcoholConsump' => $this->heavy_alcohol ?? 0,
            'AnyHealthcare' => $this->any_healthcare ?? 1,
            'NoDocbcCost' => $this->no_doc_cost ?? 0,
            'GenHlth' => $this->gen_health ?? 3,
            'MentHlth' => $this->ment_health ?? 0,
            'PhysHlth' => $this->phys_health ?? 0,
            'DiffWalk' => $this->diff_walking ?? 0,
            'Sex' => $this->sex,
            'Age' => $this->age_group ?? 9,
            'Education' => $this->education ?? 4,
            'Income' => $this->income ?? 5,
        ];
    }

    /**
     * الحصول على ملخص صحي
     */
    public function getHealthSummary(): array
    {
        return [
            'bmi_status' => $this->getBmiStatus(),
            'blood_pressure_status' => $this->getBloodPressureStatus(),
            'blood_sugar_status' => $this->getBloodSugarStatus(),
            'risk_factors' => $this->getRiskFactors(),
            'recommendations' => $this->getGeneralRecommendations(),
        ];
    }

    private function getBmiStatus(): string
    {
        $bmi = $this->bmi ?? $this->calculateBmi();

        if ($bmi < 18.5) return 'نقص الوزن';
        if ($bmi < 25) return 'طبيعي';
        if ($bmi < 30) return 'زيادة وزن';
        return 'سمنة';
    }

    private function getBloodPressureStatus(): string
    {
        if (!$this->blood_pressure_systolic || !$this->blood_pressure_diastolic) {
            return 'غير محدد';
        }

        $systolic = $this->blood_pressure_systolic;
        $diastolic = $this->blood_pressure_diastolic;

        if ($systolic < 120 && $diastolic < 80) return 'طبيعي';
        if ($systolic < 130 && $diastolic < 80) return 'مرتفع قليلاً';
        if ($systolic < 140 || $diastolic < 90) return 'مرتفع (مرحلة 1)';
        return 'مرتفع (مرحلة 2)';
    }

    private function getBloodSugarStatus(): string
    {
        $fasting = $this->blood_sugar_fasting;
        $random = $this->blood_sugar_random;

        if ($fasting) {
            if ($fasting < 100) return 'طبيعي (صائماً)';
            if ($fasting < 126) return 'ما قبل السكري (صائماً)';
            return 'سكري (صائماً)';
        }

        if ($random) {
            if ($random < 140) return 'طبيعي (عشوائي)';
            if ($random < 200) return 'ما قبل السكري (عشوائي)';
            return 'سكري (عشوائي)';
        }

        return 'غير محدد';
    }

    private function getRiskFactors(): array
    {
        $factors = [];

        if ($this->high_bp) $factors[] = 'ضغط دم مرتفع';
        if ($this->high_chol) $factors[] = 'كوليسترول عالي';
        if ($this->smoker) $factors[] = 'التدخين';
        if ($this->stroke) $factors[] = 'سكتة دماغية سابقة';
        if ($this->heart_disease) $factors[] = 'مرض قلبي';
        if (!$this->phys_activity) $factors[] = 'قلة النشاط البدني';

        $bmi = $this->bmi ?? $this->calculateBmi();
        if ($bmi > 30) $factors[] = 'سمنة';

        return $factors;
    }

    private function getGeneralRecommendations(): array
    {
        $recommendations = [];

        if ($this->high_bp) {
            $recommendations[] = 'متابعة ضغط الدم بانتظام';
            $recommendations[] = 'تقليل الملح في الطعام';
        }

        if ($this->high_chol) {
            $recommendations[] = 'فحص الكوليسترول دورياً';
            $recommendations[] = 'اتباع نظام غذائي صحي';
        }

        if ($this->smoker) {
            $recommendations[] = 'الإقلاع عن التدخين';
        }

        if (!$this->phys_activity) {
            $recommendations[] = 'ممارسة الرياضة 30 دقيقة يومياً';
        }

        $bmi = $this->bmi ?? $this->calculateBmi();
        if ($bmi > 25) {
            $recommendations[] = 'التحكم في الوزن';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'الحفاظ على نمط الحياة الصحي';
            $recommendations[] = 'فحص دوري سنوي';
        }

        return $recommendations;
    }
}
