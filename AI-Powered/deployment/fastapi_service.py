"""
FastAPI Service for Diabetes Prediction
خدمة API للتنبؤ بالسكري - جاهزة للدمج مع SHMS
"""

from fastapi import FastAPI, HTTPException, Depends, status
from fastapi.middleware.cors import CORSMiddleware
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from pydantic import BaseModel, Field, validator
from typing import Optional, List, Dict
import sys
import os
import numpy as np

# إضافة مسار models للاستيراد
sys.path.append(os.path.join(os.path.dirname(__file__), '..', 'models'))

from baseline_diabetes import DiabetesPredictor

# إنشاء FastAPI app
app = FastAPI(
    title="Diabetes Prediction API",
    description="API للتنبؤ بخطر الإصابة بالسكري - SHMS Integration",
    version="1.0.0",
    docs_url="/docs",
    redoc_url="/redoc"
)

# إعدادات CORS للسماح بالاتصال من Laravel
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # في الإنتاج: حدد domain محدد
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Security
security = HTTPBearer()

# تحميل النموذج عند بدء التشغيل
MODEL_PATH = "../models/saved/diabetes_model.pkl"
predictor = None


@app.on_event("startup")
async def load_model():
    """تحميل النموذج عند بدء الخدمة"""
    global predictor
    try:
        if os.path.exists(MODEL_PATH):
            predictor = DiabetesPredictor.load(MODEL_PATH)
            print("✅ تم تحميل النموذج بنجاح")
        else:
            print(f"⚠️ النموذج غير موجود في: {MODEL_PATH}")
            print("⚠️ يرجى تدريب النموذج أولاً باستخدام: python models/baseline_diabetes.py")
    except Exception as e:
        print(f"❌ خطأ في تحميل النموذج: {str(e)}")


# Pydantic Models للتحقق من البيانات
class PatientData(BaseModel):
    """
    بيانات المريض المطلوبة للتنبؤ
    """
    HighBP: int = Field(..., ge=0, le=1, description="ضغط دم مرتفع (0=لا, 1=نعم)")
    HighChol: int = Field(..., ge=0, le=1, description="كوليسترول عالي (0=لا, 1=نعم)")
    CholCheck: int = Field(..., ge=0, le=1, description="فحص كوليسترول في آخر 5 سنوات")
    BMI: float = Field(..., ge=10, le=100, description="مؤشر كتلة الجسم")
    Smoker: int = Field(..., ge=0, le=1, description="مدخن (0=لا, 1=نعم)")
    Stroke: int = Field(..., ge=0, le=1, description="سكتة دماغية سابقة")
    HeartDiseaseorAttack: int = Field(..., ge=0, le=1, description="مرض قلبي أو نوبة قلبية")
    PhysActivity: int = Field(..., ge=0, le=1, description="نشاط بدني في آخر 30 يوم")
    Fruits: int = Field(..., ge=0, le=1, description="تناول فواكه يومياً")
    Veggies: int = Field(..., ge=0, le=1, description="تناول خضار يومياً")
    HvyAlcoholConsump: int = Field(..., ge=0, le=1, description="استهلاك كحول عالي")
    AnyHealthcare: int = Field(..., ge=0, le=1, description="وجود تأمين صحي")
    NoDocbcCost: int = Field(..., ge=0, le=1, description="عدم زيارة طبيب بسبب التكلفة")
    GenHlth: int = Field(..., ge=1, le=5, description="الصحة العامة (1=ممتاز, 5=سيء)")
    MentHlth: float = Field(..., ge=0, le=30, description="أيام صحة نفسية سيئة في آخر 30 يوم")
    PhysHlth: float = Field(..., ge=0, le=30, description="أيام صحة جسدية سيئة في آخر 30 يوم")
    DiffWalk: int = Field(..., ge=0, le=1, description="صعوبة في المشي")
    Sex: int = Field(..., ge=0, le=1, description="الجنس (0=أنثى, 1=ذكر)")
    Age: int = Field(..., ge=1, le=13, description="الفئة العمرية (1-13)")
    Education: int = Field(..., ge=1, le=6, description="المستوى التعليمي (1-6)")
    Income: int = Field(..., ge=1, le=8, description="مستوى الدخل (1-8)")
    
    class Config:
        schema_extra = {
            "example": {
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
            }
        }


class PredictionResponse(BaseModel):
    """
    استجابة التنبؤ
    """
    success: bool
    prediction: int = Field(..., description="التنبؤ (0=لا يوجد سكري, 1=سكري/prediabetes)")
    probability: float = Field(..., description="احتمالية الإصابة")
    risk_level: str = Field(..., description="مستوى الخطر (منخفض/متوسط/عالي)")
    confidence: float = Field(..., description="ثقة النموذج")
    recommendations: List[str] = Field(..., description="توصيات طبية")
    top_risk_factors: Optional[List[Dict]] = Field(None, description="أهم عوامل الخطر")
    
    class Config:
        schema_extra = {
            "example": {
                "success": True,
                "prediction": 1,
                "probability": 0.73,
                "risk_level": "عالي",
                "confidence": 0.85,
                "recommendations": [
                    "يُنصح بإجراء فحص سكر الدم",
                    "مراجعة طبيب متخصص",
                    "تحسين النظام الغذائي"
                ],
                "top_risk_factors": [
                    {"factor": "BMI", "value": 28.5, "impact": "عالي"},
                    {"factor": "HighBP", "value": 1, "impact": "متوسط"}
                ]
            }
        }


class HealthStatus(BaseModel):
    """حالة الخدمة"""
    status: str
    model_loaded: bool
    model_type: Optional[str]
    version: str


# Helper Functions
def verify_token(credentials: HTTPAuthorizationCredentials = Depends(security)):
    """
    التحقق من JWT token
    في الإنتاج: استبدل بـ JWT validation حقيقي
    """
    token = credentials.credentials
    
    # TODO: استبدل بـ JWT validation حقيقي
    # مثال مبسط:
    if token != "demo_token_12345":
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Invalid authentication token"
        )
    
    return token


def calculate_risk_level(probability: float) -> str:
    """حساب مستوى الخطر"""
    if probability < 0.3:
        return "منخفض"
    elif probability < 0.6:
        return "متوسط"
    else:
        return "عالي"


def generate_recommendations(patient_data: dict, probability: float) -> List[str]:
    """توليد توصيات طبية بناءً على البيانات"""
    recommendations = []
    
    if probability > 0.5:
        recommendations.append("⚠️ يُنصح بإجراء فحص سكر الدم (HbA1c)")
        recommendations.append("📋 مراجعة طبيب متخصص في أقرب وقت")
    
    if patient_data.get('BMI', 0) > 30:
        recommendations.append("🏃 تقليل الوزن من خلال نظام غذائي صحي وممارسة الرياضة")
    
    if patient_data.get('HighBP', 0) == 1:
        recommendations.append("💊 متابعة ضغط الدم بانتظام")
    
    if patient_data.get('PhysActivity', 1) == 0:
        recommendations.append("🚶 ممارسة نشاط بدني لمدة 30 دقيقة يومياً")
    
    if patient_data.get('Smoker', 0) == 1:
        recommendations.append("🚭 الإقلاع عن التدخين")
    
    if patient_data.get('Fruits', 1) == 0 or patient_data.get('Veggies', 1) == 0:
        recommendations.append("🥗 تناول الفواكه والخضروات يومياً")
    
    if not recommendations:
        recommendations.append("✅ الحفاظ على نمط الحياة الصحي الحالي")
        recommendations.append("📅 فحص دوري سنوي")
    
    return recommendations


# API Endpoints
@app.get("/", tags=["General"])
async def root():
    """الصفحة الرئيسية"""
    return {
        "message": "Diabetes Prediction API - SHMS Integration",
        "version": "1.0.0",
        "docs": "/docs",
        "health": "/health"
    }


@app.get("/health", response_model=HealthStatus, tags=["General"])
async def health_check():
    """فحص حالة الخدمة"""
    return HealthStatus(
        status="healthy" if predictor is not None else "model_not_loaded",
        model_loaded=predictor is not None,
        model_type=predictor.model_type if predictor else None,
        version="1.0.0"
    )


@app.post("/predict", response_model=PredictionResponse, tags=["Prediction"])
async def predict_diabetes(
    patient: PatientData,
    # credentials: HTTPAuthorizationCredentials = Depends(security)  # تفعيل في الإنتاج
):
    """
    التنبؤ بخطر الإصابة بالسكري
    
    - **patient**: بيانات المريض (21 ميزة)
    - **Returns**: التنبؤ + الاحتمالية + التوصيات
    """
    # التحقق من تحميل النموذج
    if predictor is None:
        raise HTTPException(
            status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
            detail="النموذج غير محمل. يرجى تدريب النموذج أولاً."
        )
    
    try:
        # تحويل البيانات إلى dict
        patient_dict = patient.dict()
        
        # التنبؤ
        prediction, probabilities = predictor.predict(
            patient_dict, 
            return_proba=True
        )
        
        # استخراج النتائج
        pred_label = int(prediction[0])
        prob_positive = float(probabilities[0][1])  # احتمالية الفئة الإيجابية
        confidence = float(max(probabilities[0]))
        
        # حساب مستوى الخطر
        risk_level = calculate_risk_level(prob_positive)
        
        # توليد التوصيات
        recommendations = generate_recommendations(patient_dict, prob_positive)
        
        # أهم عوامل الخطر (مبسط - يمكن استخدام SHAP لاحقاً)
        risk_factors = []
        if patient.BMI > 30:
            risk_factors.append({"factor": "BMI", "value": patient.BMI, "impact": "عالي"})
        if patient.HighBP == 1:
            risk_factors.append({"factor": "ضغط الدم المرتفع", "value": 1, "impact": "متوسط"})
        if patient.HighChol == 1:
            risk_factors.append({"factor": "الكوليسترول العالي", "value": 1, "impact": "متوسط"})
        
        # إنشاء الاستجابة
        response = PredictionResponse(
            success=True,
            prediction=pred_label,
            probability=round(prob_positive, 4),
            risk_level=risk_level,
            confidence=round(confidence, 4),
            recommendations=recommendations,
            top_risk_factors=risk_factors if risk_factors else None
        )
        
        return response
        
    except Exception as e:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"خطأ في التنبؤ: {str(e)}"
        )


@app.post("/predict/batch", tags=["Prediction"])
async def predict_batch(
    patients: List[PatientData],
    # credentials: HTTPAuthorizationCredentials = Depends(security)
):
    """
    التنبؤ لعدة مرضى دفعة واحدة
    
    - **patients**: قائمة بيانات المرضى
    - **Returns**: قائمة التنبؤات
    """
    if predictor is None:
        raise HTTPException(
            status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
            detail="النموذج غير محمل"
        )
    
    if len(patients) > 100:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="الحد الأقصى 100 مريض في الدفعة الواحدة"
        )
    
    results = []
    for patient in patients:
        try:
            result = await predict_diabetes(patient)
            results.append(result)
        except Exception as e:
            results.append({
                "success": False,
                "error": str(e)
            })
    
    return {"total": len(patients), "results": results}


@app.get("/model/info", tags=["Model"])
async def model_info():
    """معلومات عن النموذج"""
    if predictor is None:
        raise HTTPException(
            status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
            detail="النموذج غير محمل"
        )
    
    return {
        "model_type": predictor.model_type,
        "features_count": len(predictor.feature_names) if predictor.feature_names else 0,
        "training_history": predictor.training_history
    }


@app.get("/model/features", tags=["Model"])
async def get_features():
    """الحصول على قائمة الميزات المطلوبة"""
    if predictor is None:
        raise HTTPException(
            status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
            detail="النموذج غير محمل"
        )
    
    return {
        "features": predictor.feature_names if predictor.feature_names else [],
        "total": len(predictor.feature_names) if predictor.feature_names else 0
    }


# تشغيل الخدمة
if __name__ == "__main__":
    import uvicorn
    
    print("="*80)
    print("🚀 تشغيل Diabetes Prediction API")
    print("="*80)
    print("\n📍 الوصول إلى:")
    print("   - API Docs: http://localhost:8001/docs")
    print("   - ReDoc: http://localhost:8001/redoc")
    print("   - Health Check: http://localhost:8001/health")
    print("\n⚠️ ملاحظة: تأكد من تدريب النموذج أولاً!")
    print("="*80 + "\n")
    
    uvicorn.run(
        "fastapi_service:app",
        host="0.0.0.0",
        port=8001,
        reload=True,
        log_level="info"
    )
