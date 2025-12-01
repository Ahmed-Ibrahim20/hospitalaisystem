"""
Preprocessing Pipeline for Diabetes Prediction
معالجة البيانات وتحضيرها للنموذج
"""

import pandas as pd
import numpy as np
from sklearn.base import BaseEstimator, TransformerMixin
from sklearn.preprocessing import StandardScaler
from sklearn.pipeline import Pipeline
import joblib
import os


class FeatureEngineer(BaseEstimator, TransformerMixin):
    """
    إنشاء ميزات جديدة من البيانات الموجودة
    """
    
    def __init__(self):
        self.feature_names = None
    
    def fit(self, X, y=None):
        return self
    
    def transform(self, X):
        """
        إنشاء ميزات مركبة جديدة
        """
        X_copy = X.copy()
        
        # 1. مؤشر خطر القلب والأوعية الدموية
        X_copy['cardio_risk'] = (
            X_copy['HighBP'].astype(int) + 
            X_copy['HighChol'].astype(int) + 
            X_copy['HeartDiseaseorAttack'].astype(int) +
            X_copy['Stroke'].astype(int)
        )
        
        # 2. مؤشر السلوك غير الصحي
        X_copy['unhealthy_behavior'] = (
            X_copy['Smoker'].astype(int) + 
            X_copy['HvyAlcoholConsump'].astype(int) +
            (X_copy['BMI'] > 30).astype(int) +
            (1 - X_copy['PhysActivity'].astype(int))
        )
        
        # 3. مؤشر التغذية الصحية
        X_copy['healthy_diet'] = (
            X_copy['Fruits'].astype(int) + 
            X_copy['Veggies'].astype(int)
        )
        
        # 4. مؤشر الحواجز الاجتماعية
        X_copy['social_barriers'] = (
            X_copy['NoDocbcCost'].astype(int) +
            (1 - X_copy['AnyHealthcare'].astype(int))
        )
        
        # 5. مؤشر الصحة العامة السيئة
        X_copy['poor_health_days'] = (
            X_copy['MentHlth'] + X_copy['PhysHlth']
        )
        
        # 6. فئة BMI
        X_copy['BMI_category'] = pd.cut(
            X_copy['BMI'], 
            bins=[0, 18.5, 25, 30, 100],
            labels=[0, 1, 2, 3]  # underweight, normal, overweight, obese
        ).astype(float)
        
        # 7. تفاعل العمر مع BMI
        X_copy['age_bmi_interaction'] = X_copy['Age'] * X_copy['BMI']
        
        # 8. مؤشر الخطر الكلي
        X_copy['total_risk_score'] = (
            X_copy['cardio_risk'] * 2 +
            X_copy['unhealthy_behavior'] * 1.5 +
            X_copy['GenHlth'] +
            (X_copy['BMI'] > 30).astype(int) * 2
        )
        
        return X_copy
    
    def get_feature_names_out(self, input_features=None):
        """للتوافق مع sklearn pipeline"""
        if input_features is None:
            return self.feature_names
        
        new_features = [
            'cardio_risk', 'unhealthy_behavior', 'healthy_diet',
            'social_barriers', 'poor_health_days', 'BMI_category',
            'age_bmi_interaction', 'total_risk_score'
        ]
        
        return list(input_features) + new_features


class DataValidator(BaseEstimator, TransformerMixin):
    """
    التحقق من صحة البيانات وتنظيفها
    """
    
    def __init__(self):
        self.expected_columns = [
            'HighBP', 'HighChol', 'CholCheck', 'BMI', 'Smoker', 'Stroke',
            'HeartDiseaseorAttack', 'PhysActivity', 'Fruits', 'Veggies',
            'HvyAlcoholConsump', 'AnyHealthcare', 'NoDocbcCost', 'GenHlth',
            'MentHlth', 'PhysHlth', 'DiffWalk', 'Sex', 'Age', 'Education', 'Income'
        ]
        self.value_ranges = {
            'BMI': (10, 100),
            'MentHlth': (0, 30),
            'PhysHlth': (0, 30),
            'GenHlth': (1, 5),
            'Age': (1, 13),
            'Education': (1, 6),
            'Income': (1, 8)
        }
    
    def fit(self, X, y=None):
        return self
    
    def transform(self, X):
        """
        التحقق من البيانات وتصحيح القيم الشاذة
        """
        X_copy = X.copy()
        
        # 1. التحقق من وجود الأعمدة المطلوبة
        missing_cols = set(self.expected_columns) - set(X_copy.columns)
        if missing_cols:
            raise ValueError(f"أعمدة مفقودة: {missing_cols}")
        
        # 2. معالجة القيم المفقودة
        for col in X_copy.columns:
            if X_copy[col].isna().any():
                if col in ['BMI', 'MentHlth', 'PhysHlth']:
                    # استخدام median للقيم العددية
                    X_copy[col].fillna(X_copy[col].median(), inplace=True)
                else:
                    # استخدام mode للقيم الفئوية
                    X_copy[col].fillna(X_copy[col].mode()[0], inplace=True)
        
        # 3. تصحيح القيم الشاذة
        for col, (min_val, max_val) in self.value_ranges.items():
            if col in X_copy.columns:
                X_copy[col] = X_copy[col].clip(min_val, max_val)
        
        # 4. التأكد من أن القيم الثنائية هي 0 أو 1
        binary_cols = [
            'HighBP', 'HighChol', 'CholCheck', 'Smoker', 'Stroke',
            'HeartDiseaseorAttack', 'PhysActivity', 'Fruits', 'Veggies',
            'HvyAlcoholConsump', 'AnyHealthcare', 'NoDocbcCost', 'DiffWalk', 'Sex'
        ]
        for col in binary_cols:
            if col in X_copy.columns:
                X_copy[col] = X_copy[col].clip(0, 1).round()
        
        return X_copy


def create_preprocessing_pipeline(scale_features=True):
    """
    إنشاء pipeline كامل للمعالجة
    
    Parameters:
    -----------
    scale_features : bool
        هل نستخدم StandardScaler (مفيد لـ Logistic/Neural Networks)
        
    Returns:
    --------
    Pipeline object
    """
    steps = [
        ('validator', DataValidator()),
        ('feature_engineer', FeatureEngineer())
    ]
    
    if scale_features:
        steps.append(('scaler', StandardScaler()))
    
    return Pipeline(steps)


def load_and_prepare_data(file_path, target_col='Diabetes_binary', test_size=0.2, random_state=42):
    """
    تحميل البيانات وتقسيمها
    
    Parameters:
    -----------
    file_path : str
        مسار ملف CSV
    target_col : str
        اسم عمود الهدف
    test_size : float
        نسبة بيانات الاختبار
    random_state : int
        seed للتكرارية
        
    Returns:
    --------
    X_train, X_test, y_train, y_test
    """
    from sklearn.model_selection import train_test_split
    
    # تحميل البيانات
    df = pd.read_csv(file_path)
    
    # فصل الميزات والهدف
    if target_col in df.columns:
        y = df[target_col]
        X = df.drop(columns=[target_col])
    else:
        raise ValueError(f"عمود الهدف '{target_col}' غير موجود في البيانات")
    
    # تقسيم البيانات
    X_train, X_test, y_train, y_test = train_test_split(
        X, y, 
        test_size=test_size, 
        stratify=y,
        random_state=random_state
    )
    
    return X_train, X_test, y_train, y_test


def save_pipeline(pipeline, filepath='models/saved/preprocessing_pipeline.pkl'):
    """
    حفظ pipeline للاستخدام لاحقاً
    """
    os.makedirs(os.path.dirname(filepath), exist_ok=True)
    joblib.dump(pipeline, filepath)
    print(f"✅ تم حفظ Pipeline في: {filepath}")


def load_pipeline(filepath='models/saved/preprocessing_pipeline.pkl'):
    """
    تحميل pipeline محفوظ
    """
    if not os.path.exists(filepath):
        raise FileNotFoundError(f"Pipeline غير موجود في: {filepath}")
    
    pipeline = joblib.load(filepath)
    print(f"✅ تم تحميل Pipeline من: {filepath}")
    return pipeline


# مثال على الاستخدام
if __name__ == "__main__":
    print("=" * 80)
    print("اختبار Preprocessing Pipeline")
    print("=" * 80)
    
    # تحميل البيانات
    data_path = "../DataSet/diabetes_binary_health_indicators_BRFSS2015.csv"
    
    if os.path.exists(data_path):
        X_train, X_test, y_train, y_test = load_and_prepare_data(data_path)
        
        print(f"\n📊 حجم البيانات:")
        print(f"   Training: {X_train.shape}")
        print(f"   Testing: {X_test.shape}")
        
        # إنشاء وتدريب pipeline
        pipeline = create_preprocessing_pipeline(scale_features=False)
        X_train_processed = pipeline.fit_transform(X_train)
        X_test_processed = pipeline.transform(X_test)
        
        print(f"\n✅ بعد المعالجة:")
        print(f"   Training: {X_train_processed.shape}")
        print(f"   Testing: {X_test_processed.shape}")
        
        # حفظ pipeline
        save_pipeline(pipeline)
        
        print("\n✅ اكتمل الاختبار بنجاح!")
    else:
        print(f"❌ الملف غير موجود: {data_path}")
