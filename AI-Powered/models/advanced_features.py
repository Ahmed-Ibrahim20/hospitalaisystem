"""
Advanced Feature Engineering for Medical Diagnosis
هندسة ميزات متقدمة للتشخيص الطبي
"""

import pandas as pd
import numpy as np
from sklearn.base import BaseEstimator, TransformerMixin


class MedicalFeatureEngineer(BaseEstimator, TransformerMixin):
    """
    هندسة ميزات طبية متقدمة
    - نسب طبية (Ratios)
    - علامات خطر (Risk Flags)
    - مؤشرات مركبة (Composite Scores)
    """
    
    def __init__(self):
        self.feature_names_out = None
    
    def fit(self, X, y=None):
        return self
    
    def transform(self, X):
        """إنشاء ميزات طبية متقدمة"""
        X_copy = X.copy()
        
        # ==================== 1. النسب الطبية ====================
        
        # نسبة الصحة العامة إلى العمر (كلما زادت كلما أسوأ)
        X_copy['health_age_ratio'] = X_copy['GenHlth'] / (X_copy['Age'] + 1)
        
        # نسبة الأيام السيئة إلى الشهر
        X_copy['bad_days_ratio'] = (X_copy['MentHlth'] + X_copy['PhysHlth']) / 30.0
        X_copy['bad_days_ratio'] = X_copy['bad_days_ratio'].clip(0, 2)  # تحديد الحد الأقصى
        
        # نسبة BMI إلى النشاط البدني (مؤشر خطر)
        X_copy['bmi_activity_ratio'] = X_copy['BMI'] / (X_copy['PhysActivity'] + 0.5)
        
        # ==================== 2. علامات الخطر ====================
        
        # خطر عالي: عمر فوق 65 (Age >= 11 في البيانات)
        X_copy['high_age_risk'] = (X_copy['Age'] >= 11).astype(int)
        
        # خطر السمنة (BMI > 30)
        X_copy['obesity_flag'] = (X_copy['BMI'] > 30).astype(int)
        
        # خطر السمنة المفرطة (BMI > 35)
        X_copy['severe_obesity_flag'] = (X_copy['BMI'] > 35).astype(int)
        
        # خطر نقص الوزن (BMI < 18.5)
        X_copy['underweight_flag'] = (X_copy['BMI'] < 18.5).astype(int)
        
        # خطر الصحة النفسية (أكثر من 14 يوم سيء)
        X_copy['mental_health_risk'] = (X_copy['MentHlth'] > 14).astype(int)
        
        # خطر الصحة الجسدية (أكثر من 14 يوم سيء)
        X_copy['physical_health_risk'] = (X_copy['PhysHlth'] > 14).astype(int)
        
        # خطر عدم الرعاية الصحية
        X_copy['no_healthcare_risk'] = (
            (X_copy['AnyHealthcare'] == 0) | (X_copy['NoDocbcCost'] == 1)
        ).astype(int)
        
        # ==================== 3. المؤشرات المركبة ====================
        
        # مؤشر خطر القلب والأوعية الدموية الموسع
        X_copy['cardio_risk_extended'] = (
            X_copy['HighBP'].astype(int) * 2 +
            X_copy['HighChol'].astype(int) * 2 +
            X_copy['HeartDiseaseorAttack'].astype(int) * 3 +
            X_copy['Stroke'].astype(int) * 3 +
            X_copy['obesity_flag'] * 1
        )
        
        # مؤشر نمط الحياة غير الصحي الموسع
        X_copy['unhealthy_lifestyle_score'] = (
            X_copy['Smoker'].astype(int) * 2 +
            X_copy['HvyAlcoholConsump'].astype(int) * 2 +
            (1 - X_copy['PhysActivity'].astype(int)) * 2 +
            (1 - X_copy['Fruits'].astype(int)) * 1 +
            (1 - X_copy['Veggies'].astype(int)) * 1 +
            X_copy['obesity_flag'] * 2
        )
        
        # مؤشر الصحة العامة السيئة
        X_copy['poor_health_score'] = (
            X_copy['GenHlth'] * 2 +
            (X_copy['MentHlth'] / 10) +
            (X_copy['PhysHlth'] / 10) +
            X_copy['DiffWalk'].astype(int) * 2
        )
        
        # مؤشر العوامل الاجتماعية والاقتصادية
        X_copy['socioeconomic_risk'] = (
            X_copy['NoDocbcCost'].astype(int) * 3 +
            (1 - X_copy['AnyHealthcare'].astype(int)) * 3 +
            (8 - X_copy['Income']) / 2 +  # دخل منخفض = خطر أعلى
            (6 - X_copy['Education']) / 2  # تعليم منخفض = خطر أعلى
        )
        
        # مؤشر التغذية الصحية
        X_copy['nutrition_score'] = (
            X_copy['Fruits'].astype(int) * 2 +
            X_copy['Veggies'].astype(int) * 2 +
            X_copy['CholCheck'].astype(int) * 1
        )
        
        # ==================== 4. التفاعلات بين الميزات ====================
        
        # تفاعل العمر مع BMI
        #موشر كتلة الجسم 
        X_copy['age_bmi_interaction'] = X_copy['Age'] * X_copy['BMI']
        
        # تفاعل العمر مع ضغط الدم
        X_copy['age_bp_interaction'] = X_copy['Age'] * X_copy['HighBP']
        
        # تفاعل BMI مع النشاط البدني
        X_copy['bmi_activity_interaction'] = X_copy['BMI'] * (1 - X_copy['PhysActivity'])
        
        # تفاعل الصحة العامة مع العمر
        X_copy['health_age_interaction'] = X_copy['GenHlth'] * X_copy['Age']
        
        # ==================== 5. فئات BMI ====================
        
        # تصنيف BMI حسب WHO
        #موشر الصحة العالميه
        X_copy['bmi_category'] = pd.cut(
            X_copy['BMI'],
            bins=[0, 18.5, 25, 30, 35, 100],
            labels=[0, 1, 2, 3, 4]  # underweight, normal, overweight, obese, severely obese
        ).astype(float)
        
        # ==================== 6. مؤشر الخطر الكلي ====================
        
        # مؤشر الخطر الشامل (0-100)
        X_copy['total_risk_score'] = (
            X_copy['cardio_risk_extended'] * 3 +
            X_copy['unhealthy_lifestyle_score'] * 2 +
            X_copy['poor_health_score'] * 2 +
            X_copy['socioeconomic_risk'] * 1 +
            X_copy['high_age_risk'] * 5 +
            X_copy['obesity_flag'] * 3
        )
        
        # تطبيع مؤشر الخطر الكلي (0-100)
        max_score = X_copy['total_risk_score'].max()
        if max_score > 0:
            X_copy['total_risk_score_normalized'] = (
                X_copy['total_risk_score'] / max_score * 100
            )
        else:
            X_copy['total_risk_score_normalized'] = 0
        
        # ==================== 7. عدد عوامل الخطر ====================
        
        # عدد عوامل الخطر الموجودة
        risk_factors = [
            'HighBP', 'HighChol', 'Smoker', 'Stroke', 
            'HeartDiseaseorAttack', 'obesity_flag', 
            'high_age_risk', 'mental_health_risk', 'physical_health_risk'
        ]
        
        X_copy['risk_factors_count'] = sum(
            X_copy[col].astype(int) for col in risk_factors if col in X_copy.columns
        )
        
        return X_copy
    
    def get_feature_names_out(self, input_features=None):
        """الحصول على أسماء الميزات الناتجة"""
        new_features = [
            # النسب
            'health_age_ratio', 'bad_days_ratio', 'bmi_activity_ratio',
            # علامات الخطر
            'high_age_risk', 'obesity_flag', 'severe_obesity_flag', 'underweight_flag',
            'mental_health_risk', 'physical_health_risk', 'no_healthcare_risk',
            # المؤشرات المركبة
            'cardio_risk_extended', 'unhealthy_lifestyle_score', 'poor_health_score',
            'socioeconomic_risk', 'nutrition_score',
            # التفاعلات
            'age_bmi_interaction', 'age_bp_interaction', 'bmi_activity_interaction',
            'health_age_interaction',
            # الفئات
            'bmi_category',
            # المؤشر الكلي
            'total_risk_score', 'total_risk_score_normalized', 'risk_factors_count'
        ]
        
        if input_features is not None:
            return list(input_features) + new_features
        return new_features


class TimeSeriesFeatureEngineer(BaseEstimator, TransformerMixin):
    """
    هندسة ميزات زمنية (للاستخدام المستقبلي مع بيانات متعددة الزيارات)
    """
    
    def __init__(self, window_size=3):
        self.window_size = window_size
    
    def fit(self, X, y=None):
        return self
    
    def transform(self, X):
        """
        إنشاء ميزات زمنية
        ملاحظة: يتطلب بيانات مرتبة زمنياً مع patient_id
        """
        X_copy = X.copy()
        
        # هذه الميزات تحتاج بيانات زمنية متعددة
        # حالياً نضع placeholders للاستخدام المستقبلي
        
        # دلتا درجة الحرارة (يحتاج بيانات سابقة)
        # X_copy['delta_temperature'] = 0
        
        # دلتا BMI (يحتاج بيانات سابقة)
        # X_copy['delta_bmi'] = 0
        
        # عدد الزيارات في آخر 6 أشهر (يحتاج بيانات تاريخية)
        # X_copy['visit_count_6m'] = 0
        
        return X_copy


# مثال على الاستخدام
if __name__ == "__main__":
    print("="*80)
    print("🔬 اختبار Advanced Feature Engineering")
    print("="*80)
    
    # إنشاء بيانات تجريبية
    sample_data = pd.DataFrame({
        'HighBP': [1, 0, 1],
        'HighChol': [1, 0, 0],
        'BMI': [28.5, 22.0, 35.2],
        'Smoker': [0, 1, 0],
        'Stroke': [0, 0, 0],
        'HeartDiseaseorAttack': [0, 0, 1],
        'PhysActivity': [1, 1, 0],
        'Fruits': [1, 0, 1],
        'Veggies': [1, 1, 0],
        'HvyAlcoholConsump': [0, 0, 0],
        'AnyHealthcare': [1, 1, 0],
        'NoDocbcCost': [0, 0, 1],
        'GenHlth': [3, 2, 4],
        'MentHlth': [5, 0, 20],
        'PhysHlth': [10, 0, 15],
        'DiffWalk': [0, 0, 1],
        'Sex': [1, 0, 1],
        'Age': [9, 5, 12],
        'Education': [4, 6, 3],
        'Income': [6, 8, 2],
        'CholCheck': [1, 1, 1]
    })
    
    # تطبيق Feature Engineering
    engineer = MedicalFeatureEngineer()
    enhanced_data = engineer.fit_transform(sample_data)
    
    print(f"\n📊 الميزات الأصلية: {sample_data.shape[1]}")
    print(f"✅ الميزات بعد التحسين: {enhanced_data.shape[1]}")
    print(f"🆕 ميزات جديدة: {enhanced_data.shape[1] - sample_data.shape[1]}")
    
    # عرض بعض الميزات الجديدة
    new_features = [
        'cardio_risk_extended', 'unhealthy_lifestyle_score',
        'total_risk_score_normalized', 'risk_factors_count'
    ]
    
    print("\n📋 أمثلة على الميزات الجديدة:")
    for feat in new_features:
        if feat in enhanced_data.columns:
            print(f"\n{feat}:")
            print(enhanced_data[feat].values)
    
    print("\n✅ اكتمل الاختبار بنجاح!")
