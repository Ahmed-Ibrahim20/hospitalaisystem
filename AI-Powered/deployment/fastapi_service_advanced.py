"""
Advanced FastAPI Service with SHAP Explainability
خدمة API متقدمة مع تفسير SHAP
"""

from fastapi import FastAPI, HTTPException, Depends, status, BackgroundTasks
from fastapi.middleware.cors import CORSMiddleware
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from pydantic import BaseModel, Field
from typing import Optional, List, Dict, Any
import sys
import os
import numpy as np
from datetime import datetime

# إضافة مسار models
sys.path.append(os.path.join(os.path.dirname(__file__), '..', 'models'))

try:
    from advanced_model import AdvancedDiabetesPredictor
    ADVANCED_MODEL_AVAILABLE = True
except:
    from baseline_diabetes import DiabetesPredictor
    ADVANCED_MODEL_AVAILABLE = False

from monitoring import ModelMonitor

# إنشاء FastAPI app
app = FastAPI(
    title="Advanced Diabetes Prediction API",
    description="API متقدم للتنبؤ بالسكري مع SHAP Explainability",
    version="2.0.0",
    docs_url="/docs",
    redoc_url="/redoc"
)

# CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Security
security = HTTPBearer()

# النموذج والمراقبة
MODEL_PATH = "../models/saved/advanced_diabetes_model.pkl"
predictor = None
monitor = ModelMonitor()


@app.on_event("startup")
async def load_model():
    """تحميل النموذج عند بدء الخدمة"""
    global predictor
    try:
        if ADVANCED_MODEL_AVAILABLE and os.path.exists(MODEL_PATH):
            predictor = AdvancedDiabetesPredictor.load(MODEL_PATH)
            print("✅ تم تحميل النموذج المتقدم")
        else:
            # fallback للنموذج الأساسي
            basic_path = "../models/saved/diabetes_model.pkl"
            if os.path.exists(basic_path):
                from baseline_diabetes import DiabetesPredictor
                predictor = DiabetesPredictor.load(basic_path)
                print("✅ تم تحميل النموذج الأساسي")
            else:
                print("⚠️ لا يوجد نموذج محفوظ")
    except Exception as e:
        print(f"❌ خطأ في تحميل النموذج: {str(e)}")


# Pydantic Models
class PatientData(BaseModel):
    """بيانات المريض"""
    HighBP: int = Field(..., ge=0, le=1)
    HighChol: int = Field(..., ge=0, le=1)
    CholCheck: int = Field(..., ge=0, le=1)
    BMI: float = Field(..., ge=10, le=100)
    Smoker: int = Field(..., ge=0, le=1)
    Stroke: int = Field(..., ge=0, le=1)
    HeartDiseaseorAttack: int = Field(..., ge=0, le=1)
    PhysActivity: int = Field(..., ge=0, le=1)
    Fruits: int = Field(..., ge=0, le=1)
    Veggies: int = Field(..., ge=0, le=1)
    HvyAlcoholConsump: int = Field(..., ge=0, le=1)
    AnyHealthcare: int = Field(..., ge=0, le=1)
    NoDocbcCost: int = Field(..., ge=0, le=1)
    GenHlth: int = Field(..., ge=1, le=5)
    MentHlth: float = Field(..., ge=0, le=30)
    PhysHlth: float = Field(..., ge=0, le=30)
    DiffWalk: int = Field(..., ge=0, le=1)
    Sex: int = Field(..., ge=0, le=1)
    Age: int = Field(..., ge=1, le=13)
    Education: int = Field(..., ge=1, le=6)
    Income: int = Field(..., ge=1, le=8)
    
    patient_id: Optional[str] = None  # للتتبع


class SHAPFeature(BaseModel):
    """ميزة SHAP"""
    feature: str
    shap_value: float
    impact: str
    description: Optional[str] = None


class PredictionResponse(BaseModel):
    """استجابة التنبؤ المتقدمة"""
    success: bool
    prediction: int
    probability: float
    risk_level: str
    confidence: float
    recommendations: List[str]
    shap_explanation: Optional[List[SHAPFeature]] = None
    risk_factors: Optional[Dict[str, Any]] = None
    timestamp: str


# Helper Functions
def calculate_risk_level(probability: float) -> str:
    """حساب مستوى الخطر"""
    if probability < 0.3:
        return "منخفض"
    elif probability < 0.6:
        return "متوسط"
    else:
        return "عالي"


def generate_recommendations(patient_data: dict, probability: float, 
                            shap_features: List = None) -> List[str]:
    """توليد توصيات ذكية بناءً على SHAP"""
    recommendations = []
    
    # توصيات عامة
    if probability > 0.7:
        recommendations.append("⚠️ خطر عالي - يُنصح بإجراء فحص HbA1c فوراً")
        recommendations.append("📋 مراجعة طبيب متخصص في أقرب وقت")
    elif probability > 0.5:
        recommendations.append("⚠️ خطر متوسط - يُنصح بإجراء فحص سكر الدم")
        recommendations.append("📅 متابعة دورية كل 3 أشهر")
    
    # توصيات بناءً على SHAP (أهم العوامل)
    if shap_features:
        for feat in shap_features[:3]:  # أهم 3 عوامل
            feature_name = feat.get('feature', '') if isinstance(feat, dict) else ''
            
            if 'BMI' in feature_name or 'obesity' in feature_name.lower():
                recommendations.append("🏃 تقليل الوزن من خلال نظام غذائي صحي وممارسة الرياضة")
            elif 'BP' in feature_name or 'blood pressure' in feature_name.lower():
                recommendations.append("💊 متابعة ضغط الدم بانتظام")
            elif 'activity' in feature_name.lower():
                recommendations.append("🚶 ممارسة نشاط بدني لمدة 30 دقيقة يومياً")
            elif 'smoke' in feature_name.lower():
                recommendations.append("🚭 الإقلاع عن التدخين")
    
    # توصيات بناءً على البيانات المباشرة
    if patient_data.get('BMI', 0) > 30:
        if "وزن" not in ' '.join(recommendations):
            recommendations.append("🏃 تقليل الوزن - BMI مرتفع")
    
    if patient_data.get('PhysActivity', 1) == 0:
        if "نشاط" not in ' '.join(recommendations):
            recommendations.append("🚶 ممارسة نشاط بدني منتظم")
    
    if patient_data.get('Fruits', 1) == 0 or patient_data.get('Veggies', 1) == 0:
        recommendations.append("🥗 تناول الفواكه والخضروات يومياً")
    
    if not recommendations:
        recommendations.append("✅ الحفاظ على نمط الحياة الصحي الحالي")
        recommendations.append("📅 فحص دوري سنوي")
    
    return list(set(recommendations))  # إزالة التكرار


def analyze_risk_factors(patient_data: dict) -> Dict[str, Any]:
    """تحليل عوامل الخطر"""
    risk_factors = {}
    
    # عوامل القلب والأوعية الدموية
    cardio_score = (
        patient_data.get('HighBP', 0) * 2 +
        patient_data.get('HighChol', 0) * 2 +
        patient_data.get('HeartDiseaseorAttack', 0) * 3 +
        patient_data.get('Stroke', 0) * 3
    )
    risk_factors['cardiovascular'] = {
        'score': cardio_score,
        'level': 'عالي' if cardio_score >= 5 else 'متوسط' if cardio_score >= 2 else 'منخفض'
    }
    
    # نمط الحياة
    lifestyle_score = (
        patient_data.get('Smoker', 0) * 2 +
        patient_data.get('HvyAlcoholConsump', 0) * 2 +
        (1 - patient_data.get('PhysActivity', 1)) * 2 +
        (1 - patient_data.get('Fruits', 1)) +
        (1 - patient_data.get('Veggies', 1))
    )
    risk_factors['lifestyle'] = {
        'score': lifestyle_score,
        'level': 'غير صحي' if lifestyle_score >= 5 else 'متوسط' if lifestyle_score >= 2 else 'صحي'
    }
    
    # BMI
    bmi = patient_data.get('BMI', 0)
    if bmi > 35:
        bmi_level = 'سمنة مفرطة'
    elif bmi > 30:
        bmi_level = 'سمنة'
    elif bmi > 25:
        bmi_level = 'وزن زائد'
    elif bmi >= 18.5:
        bmi_level = 'طبيعي'
    else:
        bmi_level = 'نقص وزن'
    
    risk_factors['bmi'] = {
        'value': bmi,
        'category': bmi_level
    }
    
    # العمر
    age = patient_data.get('Age', 0)
    risk_factors['age'] = {
        'category': age,
        'high_risk': age >= 11  # 65+ سنة
    }
    
    return risk_factors


# API Endpoints
@app.get("/", tags=["General"])
async def root():
    """الصفحة الرئيسية"""
    return {
        "message": "Advanced Diabetes Prediction API with SHAP",
        "version": "2.0.0",
        "features": [
            "SHAP Explainability",
            "Advanced Feature Engineering",
            "Risk Factor Analysis",
            "Smart Recommendations"
        ],
        "docs": "/docs"
    }


@app.get("/health", tags=["General"])
async def health_check():
    """فحص حالة الخدمة"""
    return {
        "status": "healthy" if predictor is not None else "model_not_loaded",
        "model_loaded": predictor is not None,
        "model_type": getattr(predictor, 'model_type', 'unknown') if predictor else None,
        "advanced_features": ADVANCED_MODEL_AVAILABLE,
        "version": "2.0.0",
        "timestamp": datetime.now().isoformat()
    }


@app.post("/predict", response_model=PredictionResponse, tags=["Prediction"])
async def predict_diabetes(
    patient: PatientData,
    background_tasks: BackgroundTasks,
    include_shap: bool = True
):
    """
    التنبؤ بخطر السكري مع تفسير SHAP
    
    Parameters:
    -----------
    patient : PatientData
        بيانات المريض
    include_shap : bool
        تضمين تفسير SHAP (قد يبطئ الاستجابة قليلاً)
    """
    if predictor is None:
        raise HTTPException(
            status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
            detail="النموذج غير محمل"
        )
    
    try:
        patient_dict = patient.dict(exclude={'patient_id'})
        
        # التنبؤ
        if ADVANCED_MODEL_AVAILABLE and hasattr(predictor, 'explain_prediction') and include_shap:
            # استخدام النموذج المتقدم مع SHAP
            explanation = predictor.explain_prediction(patient_dict, top_n=5)
            
            prediction = explanation['prediction']
            probability = explanation['probability']
            confidence = explanation['confidence']
            shap_features = explanation.get('shap_explanation', [])
        else:
            # النموذج الأساسي
            pred, proba = predictor.predict(patient_dict, return_proba=True)
            prediction = int(pred[0])
            probability = float(proba[0][1])
            confidence = float(max(proba[0]))
            shap_features = None
        
        # حساب مستوى الخطر
        risk_level = calculate_risk_level(probability)
        
        # تحليل عوامل الخطر
        risk_factors = analyze_risk_factors(patient_dict)
        
        # توليد التوصيات
        recommendations = generate_recommendations(patient_dict, probability, shap_features)
        
        # تحويل SHAP features للـ response model
        shap_response = None
        if shap_features and isinstance(shap_features, list):
            shap_response = [
                SHAPFeature(
                    feature=f.get('feature', ''),
                    shap_value=f.get('shap_value', 0.0),
                    impact=f.get('impact', 'unknown')
                )
                for f in shap_features if isinstance(f, dict)
            ]
        
        # إنشاء الاستجابة
        response = PredictionResponse(
            success=True,
            prediction=prediction,
            probability=round(probability, 4),
            risk_level=risk_level,
            confidence=round(confidence, 4),
            recommendations=recommendations,
            shap_explanation=shap_response,
            risk_factors=risk_factors,
            timestamp=datetime.now().isoformat()
        )
        
        # تسجيل التنبؤ في الخلفية
        background_tasks.add_task(
            monitor.log_prediction,
            patient_dict,
            prediction,
            probability,
            patient.patient_id
        )
        
        return response
        
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"خطأ في التنبؤ: {str(e)}"
        )


@app.post("/predict/batch", tags=["Prediction"])
async def predict_batch(patients: List[PatientData]):
    """التنبؤ لعدة مرضى"""
    if predictor is None:
        raise HTTPException(
            status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
            detail="النموذج غير محمل"
        )
    
    if len(patients) > 100:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="الحد الأقصى 100 مريض"
        )
    
    results = []
    for patient in patients:
        try:
            result = await predict_diabetes(patient, BackgroundTasks(), include_shap=False)
            results.append(result)
        except Exception as e:
            results.append({
                "success": False,
                "error": str(e),
                "patient_id": patient.patient_id
            })
    
    return {
        "total": len(patients),
        "results": results,
        "timestamp": datetime.now().isoformat()
    }


@app.get("/model/info", tags=["Model"])
async def model_info():
    """معلومات عن النموذج"""
    if predictor is None:
        raise HTTPException(
            status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
            detail="النموذج غير محمل"
        )
    
    info = {
        "model_type": getattr(predictor, 'model_type', 'unknown'),
        "features_count": len(predictor.feature_names) if hasattr(predictor, 'feature_names') else 0,
        "advanced_features": getattr(predictor, 'use_advanced_features', False),
        "shap_available": predictor.shap_explainer is not None if hasattr(predictor, 'shap_explainer') else False
    }
    
    if hasattr(predictor, 'training_history'):
        info['training_history'] = predictor.training_history
    
    return info


@app.get("/monitoring/report", tags=["Monitoring"])
async def get_monitoring_report():
    """الحصول على تقرير المراقبة"""
    report = monitor.generate_report()
    return report


# تشغيل الخدمة
if __name__ == "__main__":
    import uvicorn
    
    print("="*80)
    print("🚀 تشغيل Advanced Diabetes Prediction API")
    print("="*80)
    print("\n📍 الميزات:")
    print("   ✅ SHAP Explainability")
    print("   ✅ Advanced Feature Engineering")
    print("   ✅ Risk Factor Analysis")
    print("   ✅ Smart Recommendations")
    print("   ✅ Performance Monitoring")
    print("\n📍 الوصول إلى:")
    print("   - API Docs: http://localhost:8000/docs")
    print("   - Health Check: http://localhost:8000/health")
    print("="*80 + "\n")
    
    uvicorn.run(
        "fastapi_service_advanced:app",
        host="0.0.0.0",
        port=8000,
        reload=True
    )
