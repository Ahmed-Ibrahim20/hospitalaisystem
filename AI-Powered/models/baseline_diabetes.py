"""
Baseline Diabetes Prediction Model
نموذج أساسي للتنبؤ بالسكري باستخدام Random Forest
"""

import pandas as pd
import numpy as np
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import (
    classification_report, 
    confusion_matrix, 
    roc_auc_score,
    roc_curve,
    precision_recall_curve,
    average_precision_score
)
import joblib
import os
import json
from datetime import datetime

# استيراد preprocessing pipeline
from preprocessing import (
    load_and_prepare_data,
    create_preprocessing_pipeline,
    save_pipeline,
    load_pipeline
)


class DiabetesPredictor:
    """
    نموذج متكامل للتنبؤ بالسكري
    """
    
    def __init__(self, model_type='random_forest', use_scaling=False):
        """
        Parameters:
        -----------
        model_type : str
            نوع النموذج ('random_forest', 'xgboost', 'lightgbm')
        use_scaling : bool
            استخدام StandardScaler
        """
        self.model_type = model_type
        self.use_scaling = use_scaling
        self.preprocessing_pipeline = None
        self.model = None
        self.feature_names = None
        self.training_history = {}
        
    def _create_model(self):
        """إنشاء النموذج حسب النوع المحدد"""
        if self.model_type == 'random_forest':
            return RandomForestClassifier(
                n_estimators=200,
                max_depth=15,
                min_samples_split=10,
                min_samples_leaf=4,
                class_weight='balanced',
                random_state=42,
                n_jobs=-1,
                verbose=1
            )
        elif self.model_type == 'xgboost':
            try:
                import xgboost as xgb
                return xgb.XGBClassifier(
                    n_estimators=200,
                    max_depth=6,
                    learning_rate=0.1,
                    subsample=0.8,
                    colsample_bytree=0.8,
                    scale_pos_weight=5,  # للتعامل مع imbalance
                    random_state=42,
                    n_jobs=-1
                )
            except ImportError:
                print("⚠️ XGBoost غير مثبت، استخدام Random Forest")
                return self._create_model_rf()
        elif self.model_type == 'lightgbm':
            try:
                import lightgbm as lgb
                return lgb.LGBMClassifier(
                    n_estimators=200,
                    max_depth=6,
                    learning_rate=0.1,
                    subsample=0.8,
                    colsample_bytree=0.8,
                    class_weight='balanced',
                    random_state=42,
                    n_jobs=-1
                )
            except ImportError:
                print("⚠️ LightGBM غير مثبت، استخدام Random Forest")
                return self._create_model_rf()
        else:
            raise ValueError(f"نوع نموذج غير معروف: {self.model_type}")
    
    def train(self, X_train, y_train, X_val=None, y_val=None):
        """
        تدريب النموذج
        
        Parameters:
        -----------
        X_train : DataFrame
            بيانات التدريب
        y_train : Series
            الأهداف
        X_val : DataFrame (optional)
            بيانات التحقق
        y_val : Series (optional)
            أهداف التحقق
        """
        print("\n" + "="*80)
        print("🚀 بدء التدريب...")
        print("="*80)
        
        # إنشاء preprocessing pipeline
        print("\n1️⃣ إنشاء Preprocessing Pipeline...")
        self.preprocessing_pipeline = create_preprocessing_pipeline(
            scale_features=self.use_scaling
        )
        
        # معالجة البيانات
        print("2️⃣ معالجة البيانات...")
        X_train_processed = self.preprocessing_pipeline.fit_transform(X_train)
        
        # حفظ أسماء الميزات
        if hasattr(X_train_processed, 'columns'):
            self.feature_names = X_train_processed.columns.tolist()
        else:
            # في حالة numpy array
            self.feature_names = [f"feature_{i}" for i in range(X_train_processed.shape[1])]
        
        print(f"   ✅ عدد الميزات بعد المعالجة: {X_train_processed.shape[1]}")
        
        # إنشاء النموذج
        print(f"\n3️⃣ إنشاء نموذج {self.model_type}...")
        self.model = self._create_model()
        
        # التدريب
        print("4️⃣ تدريب النموذج...")
        start_time = datetime.now()
        
        self.model.fit(X_train_processed, y_train)
        
        training_time = (datetime.now() - start_time).total_seconds()
        print(f"   ✅ اكتمل التدريب في {training_time:.2f} ثانية")
        
        # حفظ معلومات التدريب
        self.training_history = {
            'model_type': self.model_type,
            'training_samples': len(X_train),
            'features_count': X_train_processed.shape[1],
            'training_time_seconds': training_time,
            'timestamp': datetime.now().isoformat()
        }
        
        # تقييم على بيانات التدريب
        print("\n5️⃣ تقييم على بيانات التدريب...")
        train_score = self.model.score(X_train_processed, y_train)
        print(f"   Training Accuracy: {train_score:.4f}")
        
        # تقييم على بيانات التحقق إن وجدت
        if X_val is not None and y_val is not None:
            print("\n6️⃣ تقييم على بيانات التحقق...")
            X_val_processed = self.preprocessing_pipeline.transform(X_val)
            val_score = self.model.score(X_val_processed, y_val)
            print(f"   Validation Accuracy: {val_score:.4f}")
            
            self.training_history['validation_accuracy'] = val_score
        
        print("\n✅ اكتمل التدريب بنجاح!")
        
    def evaluate(self, X_test, y_test, verbose=True):
        """
        تقييم شامل للنموذج
        
        Returns:
        --------
        dict : نتائج التقييم
        """
        if self.model is None:
            raise ValueError("يجب تدريب النموذج أولاً!")
        
        # معالجة البيانات
        X_test_processed = self.preprocessing_pipeline.transform(X_test)
        
        # التنبؤ
        y_pred = self.model.predict(X_test_processed)
        y_proba = self.model.predict_proba(X_test_processed)[:, 1]
        
        # حساب المقاييس
        results = {
            'accuracy': self.model.score(X_test_processed, y_test),
            'roc_auc': roc_auc_score(y_test, y_proba),
            'average_precision': average_precision_score(y_test, y_proba),
            'confusion_matrix': confusion_matrix(y_test, y_pred).tolist(),
            'classification_report': classification_report(y_test, y_pred, output_dict=True)
        }
        
        if verbose:
            print("\n" + "="*80)
            print("📊 نتائج التقييم")
            print("="*80)
            print(f"\n🎯 Accuracy: {results['accuracy']:.4f}")
            print(f"📈 ROC-AUC: {results['roc_auc']:.4f}")
            print(f"📊 Average Precision: {results['average_precision']:.4f}")
            
            print("\n📋 Classification Report:")
            print(classification_report(y_test, y_pred))
            
            print("\n🔢 Confusion Matrix:")
            cm = results['confusion_matrix']
            print(f"   TN: {cm[0][0]:6d}  |  FP: {cm[0][1]:6d}")
            print(f"   FN: {cm[1][0]:6d}  |  TP: {cm[1][1]:6d}")
        
        return results
    
    def predict(self, X, return_proba=False):
        """
        التنبؤ لبيانات جديدة
        
        Parameters:
        -----------
        X : DataFrame or dict
            البيانات للتنبؤ
        return_proba : bool
            إرجاع الاحتمالات
            
        Returns:
        --------
        predictions or (predictions, probabilities)
        """
        if self.model is None:
            raise ValueError("يجب تدريب النموذج أولاً!")
        
        # تحويل dict إلى DataFrame إذا لزم الأمر
        if isinstance(X, dict):
            X = pd.DataFrame([X])
        
        # معالجة البيانات
        X_processed = self.preprocessing_pipeline.transform(X)
        
        # التنبؤ
        predictions = self.model.predict(X_processed)
        
        if return_proba:
            probabilities = self.model.predict_proba(X_processed)
            return predictions, probabilities
        
        return predictions
    
    def get_feature_importance(self, top_n=20):
        """
        الحصول على أهمية الميزات
        
        Returns:
        --------
        DataFrame : الميزات وأهميتها
        """
        if self.model is None:
            raise ValueError("يجب تدريب النموذج أولاً!")
        
        if hasattr(self.model, 'feature_importances_'):
            importances = self.model.feature_importances_
            
            # إنشاء DataFrame
            feature_imp = pd.DataFrame({
                'feature': self.feature_names,
                'importance': importances
            }).sort_values('importance', ascending=False)
            
            return feature_imp.head(top_n)
        else:
            print("⚠️ النموذج لا يدعم feature_importances_")
            return None
    
    def save(self, model_path='models/saved/diabetes_model.pkl'):
        """حفظ النموذج والـ pipeline"""
        if self.model is None:
            raise ValueError("لا يوجد نموذج للحفظ!")
        
        os.makedirs(os.path.dirname(model_path), exist_ok=True)
        
        # حفظ كل شيء في dict واحد
        model_data = {
            'model': self.model,
            'preprocessing_pipeline': self.preprocessing_pipeline,
            'feature_names': self.feature_names,
            'model_type': self.model_type,
            'training_history': self.training_history
        }
        
        joblib.dump(model_data, model_path)
        print(f"✅ تم حفظ النموذج في: {model_path}")
        
        # حفظ metadata كـ JSON
        metadata_path = model_path.replace('.pkl', '_metadata.json')
        with open(metadata_path, 'w', encoding='utf-8') as f:
            json.dump(self.training_history, f, indent=2, ensure_ascii=False)
        print(f"✅ تم حفظ Metadata في: {metadata_path}")
    
    @classmethod
    def load(cls, model_path='models/saved/diabetes_model.pkl'):
        """تحميل نموذج محفوظ"""
        if not os.path.exists(model_path):
            raise FileNotFoundError(f"النموذج غير موجود: {model_path}")
        
        model_data = joblib.load(model_path)
        
        # إنشاء instance جديد
        predictor = cls(model_type=model_data['model_type'])
        predictor.model = model_data['model']
        predictor.preprocessing_pipeline = model_data['preprocessing_pipeline']
        predictor.feature_names = model_data['feature_names']
        predictor.training_history = model_data['training_history']
        
        print(f"✅ تم تحميل النموذج من: {model_path}")
        return predictor


# التشغيل الرئيسي
if __name__ == "__main__":
    print("="*80)
    print("🏥 Diabetes Prediction - Baseline Model")
    print("="*80)
    
    # المسارات
    data_path = "../DataSet/diabetes_binary_health_indicators_BRFSS2015.csv"
    
    if not os.path.exists(data_path):
        print(f"❌ الملف غير موجود: {data_path}")
        exit(1)
    
    # تحميل البيانات
    print("\n📂 تحميل البيانات...")
    X_train, X_test, y_train, y_test = load_and_prepare_data(
        data_path,
        test_size=0.2,
        random_state=42
    )
    
    print(f"✅ Training: {X_train.shape}, Testing: {X_test.shape}")
    print(f"✅ توزيع الهدف - Training: {y_train.value_counts().to_dict()}")
    
    # إنشاء وتدريب النموذج
    predictor = DiabetesPredictor(model_type='random_forest', use_scaling=False)
    predictor.train(X_train, y_train)
    
    # التقييم
    results = predictor.evaluate(X_test, y_test)
    
    # عرض أهمية الميزات
    print("\n" + "="*80)
    print("🔍 أهم الميزات")
    print("="*80)
    feature_imp = predictor.get_feature_importance(top_n=15)
    if feature_imp is not None:
        print(feature_imp.to_string(index=False))
    
    # حفظ النموذج
    print("\n" + "="*80)
    print("💾 حفظ النموذج")
    print("="*80)
    predictor.save()
    
    # اختبار التنبؤ لحالة واحدة
    print("\n" + "="*80)
    print("🧪 اختبار التنبؤ")
    print("="*80)
    
    sample_patient = X_test.iloc[0].to_dict()
    print(f"بيانات المريض: {sample_patient}")
    
    pred, proba = predictor.predict(sample_patient, return_proba=True)
    print(f"\n✅ التنبؤ: {'سكري' if pred[0] == 1 else 'لا يوجد سكري'}")
    print(f"✅ الاحتمال: {proba[0][1]:.2%}")
    
    print("\n" + "="*80)
    print("✅ اكتمل البرنامج بنجاح!")
    print("="*80)
