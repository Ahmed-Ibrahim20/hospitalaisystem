"""
Advanced ML Models with SHAP Explainability
نماذج متقدمة مع تفسير SHAP
"""

import pandas as pd
import numpy as np
import joblib
import os
import json
from datetime import datetime
from sklearn.model_selection import train_test_split, StratifiedKFold, cross_val_score
from sklearn.metrics import (
    classification_report, confusion_matrix, roc_auc_score,
    precision_recall_curve, average_precision_score, roc_curve
)
from sklearn.calibration import calibration_curve
import warnings
warnings.filterwarnings('ignore')

# استيراد النماذج
try:
    import xgboost as xgb
    XGBOOST_AVAILABLE = True
except ImportError:
    XGBOOST_AVAILABLE = False
    print("⚠️ XGBoost غير مثبت")

try:
    import lightgbm as lgb
    LIGHTGBM_AVAILABLE = True
except ImportError:
    LIGHTGBM_AVAILABLE = False
    print("⚠️ LightGBM غير مثبت")

try:
    import shap
    SHAP_AVAILABLE = True
except ImportError:
    SHAP_AVAILABLE = False
    print("⚠️ SHAP غير مثبت")

from sklearn.ensemble import RandomForestClassifier, VotingClassifier
from preprocessing import load_and_prepare_data, create_preprocessing_pipeline
from advanced_features import MedicalFeatureEngineer


class AdvancedDiabetesPredictor:
    """
    نموذج متقدم للتنبؤ بالسكري مع:
    - XGBoost / LightGBM / Ensemble
    - SHAP Explainability
    - Model Calibration
    - Cross-validation
    """
    
    def __init__(self, model_type='xgboost', use_advanced_features=True):
        """
        Parameters:
        -----------
        model_type : str
            'xgboost', 'lightgbm', 'random_forest', 'ensemble'
        use_advanced_features : bool
            استخدام الميزات المتقدمة
        """
        self.model_type = model_type
        self.use_advanced_features = use_advanced_features
        self.model = None
        self.preprocessing_pipeline = None
        self.feature_engineer = None
        self.feature_names = None
        self.shap_explainer = None
        self.training_history = {}
        
    def _create_model(self):
        """إنشاء النموذج"""
        if self.model_type == 'xgboost' and XGBOOST_AVAILABLE:
            return xgb.XGBClassifier(
                n_estimators=300,
                max_depth=6,
                learning_rate=0.05,
                subsample=0.8,
                colsample_bytree=0.8,
                min_child_weight=3,
                gamma=0.1,
                scale_pos_weight=5,  # للتعامل مع imbalance
                random_state=42,
                n_jobs=-1,
                eval_metric='logloss'
            )
        elif self.model_type == 'lightgbm' and LIGHTGBM_AVAILABLE:
            return lgb.LGBMClassifier(
                n_estimators=300,
                max_depth=6,
                learning_rate=0.05,
                subsample=0.8,
                colsample_bytree=0.8,
                min_child_samples=20,
                class_weight='balanced',
                random_state=42,
                n_jobs=-1,
                verbose=-1
            )
        elif self.model_type == 'ensemble':
            # Ensemble من عدة نماذج
            models = []
            
            if XGBOOST_AVAILABLE:
                models.append(('xgb', self._create_xgb()))
            if LIGHTGBM_AVAILABLE:
                models.append(('lgb', self._create_lgb()))
            models.append(('rf', self._create_rf()))
            
            return VotingClassifier(estimators=models, voting='soft', n_jobs=-1)
        else:
            # Random Forest كـ fallback
            return RandomForestClassifier(
                n_estimators=300,
                max_depth=15,
                min_samples_split=10,
                min_samples_leaf=4,
                class_weight='balanced',
                random_state=42,
                n_jobs=-1
            )
    
    def _create_xgb(self):
        """إنشاء XGBoost"""
        return xgb.XGBClassifier(
            n_estimators=200, max_depth=6, learning_rate=0.05,
            scale_pos_weight=5, random_state=42, n_jobs=-1
        )
    
    def _create_lgb(self):
        """إنشاء LightGBM"""
        return lgb.LGBMClassifier(
            n_estimators=200, max_depth=6, learning_rate=0.05,
            class_weight='balanced', random_state=42, n_jobs=-1, verbose=-1
        )
    
    def _create_rf(self):
        """إنشاء Random Forest"""
        return RandomForestClassifier(
            n_estimators=200, max_depth=15, class_weight='balanced',
            random_state=42, n_jobs=-1
        )
    
    def train(self, X_train, y_train, X_val=None, y_val=None, use_cv=True):
        """
        تدريب النموذج مع Cross-Validation
        """
        print("\n" + "="*80)
        print("🚀 بدء التدريب المتقدم...")
        print("="*80)
        
        # 1. Preprocessing
        print("\n1️⃣ معالجة البيانات...")
        self.preprocessing_pipeline = create_preprocessing_pipeline(scale_features=False)
        X_train_processed = self.preprocessing_pipeline.fit_transform(X_train)
        
        # 2. Advanced Features
        if self.use_advanced_features:
            print("2️⃣ إنشاء ميزات متقدمة...")
            self.feature_engineer = MedicalFeatureEngineer()
            X_train_processed = self.feature_engineer.fit_transform(
                pd.DataFrame(X_train_processed, columns=X_train.columns)
            )
        
        # حفظ أسماء الميزات
        if isinstance(X_train_processed, pd.DataFrame):
            self.feature_names = X_train_processed.columns.tolist()
            X_train_processed = X_train_processed.values
        else:
            self.feature_names = [f"feature_{i}" for i in range(X_train_processed.shape[1])]
        
        print(f"   ✅ عدد الميزات: {X_train_processed.shape[1]}")
        
        # 3. Cross-Validation
        if use_cv:
            print("\n3️⃣ Cross-Validation...")
            self.model = self._create_model()
            
            cv = StratifiedKFold(n_splits=5, shuffle=True, random_state=42)
            cv_scores = cross_val_score(
                self.model, X_train_processed, y_train,
                cv=cv, scoring='roc_auc', n_jobs=-1
            )
            
            print(f"   CV ROC-AUC Scores: {cv_scores}")
            print(f"   Mean: {cv_scores.mean():.4f} (+/- {cv_scores.std():.4f})")
            
            self.training_history['cv_scores'] = cv_scores.tolist()
            self.training_history['cv_mean'] = float(cv_scores.mean())
            self.training_history['cv_std'] = float(cv_scores.std())
        
        # 4. التدريب النهائي
        print("\n4️⃣ التدريب النهائي...")
        self.model = self._create_model()
        
        start_time = datetime.now()
        self.model.fit(X_train_processed, y_train)
        training_time = (datetime.now() - start_time).total_seconds()
        
        print(f"   ✅ اكتمل التدريب في {training_time:.2f} ثانية")
        
        # 5. التقييم
        train_score = self.model.score(X_train_processed, y_train)
        print(f"\n5️⃣ Training Accuracy: {train_score:.4f}")
        
        if X_val is not None and y_val is not None:
            X_val_processed = self.preprocessing_pipeline.transform(X_val)
            if self.use_advanced_features:
                X_val_processed = self.feature_engineer.transform(
                    pd.DataFrame(X_val_processed, columns=X_val.columns)
                )
                if isinstance(X_val_processed, pd.DataFrame):
                    X_val_processed = X_val_processed.values
            
            val_score = self.model.score(X_val_processed, y_val)
            print(f"   Validation Accuracy: {val_score:.4f}")
        
        # 6. SHAP Explainer
        if SHAP_AVAILABLE:
            print("\n6️⃣ إنشاء SHAP Explainer...")
            try:
                # استخدام عينة صغيرة لتسريع SHAP
                sample_size = min(1000, X_train_processed.shape[0])
                sample_indices = np.random.choice(
                    X_train_processed.shape[0], sample_size, replace=False
                )
                X_sample = X_train_processed[sample_indices]
                
                if self.model_type in ['xgboost', 'lightgbm']:
                    self.shap_explainer = shap.TreeExplainer(self.model)
                else:
                    self.shap_explainer = shap.KernelExplainer(
                        self.model.predict_proba, X_sample
                    )
                print("   ✅ SHAP Explainer جاهز")
            except Exception as e:
                print(f"   ⚠️ فشل إنشاء SHAP: {str(e)}")
        
        # حفظ معلومات التدريب
        self.training_history.update({
            'model_type': self.model_type,
            'training_samples': len(X_train),
            'features_count': X_train_processed.shape[1],
            'training_time_seconds': training_time,
            'use_advanced_features': self.use_advanced_features,
            'timestamp': datetime.now().isoformat()
        })
        
        print("\n✅ اكتمل التدريب بنجاح!")
    
    def evaluate(self, X_test, y_test, verbose=True):
        """تقييم شامل مع Calibration"""
        if self.model is None:
            raise ValueError("يجب تدريب النموذج أولاً!")
        
        # معالجة البيانات
        X_test_processed = self.preprocessing_pipeline.transform(X_test)
        if self.use_advanced_features:
            X_test_processed = self.feature_engineer.transform(
                pd.DataFrame(X_test_processed, columns=X_test.columns)
            )
            if isinstance(X_test_processed, pd.DataFrame):
                X_test_processed = X_test_processed.values
        
        # التنبؤ
        y_pred = self.model.predict(X_test_processed)
        y_proba = self.model.predict_proba(X_test_processed)[:, 1]
        
        # المقاييس
        results = {
            'accuracy': float(self.model.score(X_test_processed, y_test)),
            'roc_auc': float(roc_auc_score(y_test, y_proba)),
            'average_precision': float(average_precision_score(y_test, y_proba)),
            'confusion_matrix': confusion_matrix(y_test, y_pred).tolist(),
            'classification_report': classification_report(y_test, y_pred, output_dict=True)
        }
        
        # Calibration
        try:
            prob_true, prob_pred = calibration_curve(y_test, y_proba, n_bins=10)
            results['calibration'] = {
                'prob_true': prob_true.tolist(),
                'prob_pred': prob_pred.tolist()
            }
        except:
            pass
        
        if verbose:
            print("\n" + "="*80)
            print("📊 نتائج التقييم المتقدم")
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
    
    def explain_prediction(self, X, top_n=5):
        """
        تفسير التنبؤ باستخدام SHAP
        
        Returns:
        --------
        dict: التنبؤ + SHAP values + أهم الميزات
        """
        if self.model is None:
            raise ValueError("يجب تدريب النموذج أولاً!")
        
        # معالجة البيانات
        if isinstance(X, dict):
            X = pd.DataFrame([X])
        
        X_processed = self.preprocessing_pipeline.transform(X)
        if self.use_advanced_features:
            X_processed = self.feature_engineer.transform(
                pd.DataFrame(X_processed, columns=X.columns)
            )
            if isinstance(X_processed, pd.DataFrame):
                X_processed = X_processed.values
        
        # التنبؤ
        prediction = self.model.predict(X_processed)[0]
        probability = self.model.predict_proba(X_processed)[0]
        
        result = {
            'prediction': int(prediction),
            'probability': float(probability[1]),
            'confidence': float(max(probability))
        }
        
        # SHAP Explanation
        if self.shap_explainer is not None and SHAP_AVAILABLE:
            try:
                shap_values = self.shap_explainer.shap_values(X_processed)
                
                # أخذ SHAP values للفئة الإيجابية
                if isinstance(shap_values, list):
                    shap_values = shap_values[1]
                
                # أهم الميزات
                shap_abs = np.abs(shap_values[0])
                top_indices = np.argsort(shap_abs)[-top_n:][::-1]
                
                top_features = []
                for idx in top_indices:
                    top_features.append({
                        'feature': self.feature_names[idx],
                        'shap_value': float(shap_values[0][idx]),
                        'impact': 'positive' if shap_values[0][idx] > 0 else 'negative'
                    })
                
                result['shap_explanation'] = top_features
            except Exception as e:
                result['shap_explanation'] = f"Error: {str(e)}"
        
        return result
    
    def save(self, model_path='models/saved/advanced_diabetes_model.pkl'):
        """حفظ النموذج"""
        if self.model is None:
            raise ValueError("لا يوجد نموذج للحفظ!")
        
        os.makedirs(os.path.dirname(model_path), exist_ok=True)
        
        model_data = {
            'model': self.model,
            'preprocessing_pipeline': self.preprocessing_pipeline,
            'feature_engineer': self.feature_engineer,
            'feature_names': self.feature_names,
            'model_type': self.model_type,
            'use_advanced_features': self.use_advanced_features,
            'training_history': self.training_history,
            'shap_explainer': self.shap_explainer
        }
        
        joblib.dump(model_data, model_path)
        print(f"✅ تم حفظ النموذج في: {model_path}")
        
        # حفظ metadata
        metadata_path = model_path.replace('.pkl', '_metadata.json')
        with open(metadata_path, 'w', encoding='utf-8') as f:
            # إزالة shap_explainer من metadata (لا يمكن تحويله لـ JSON)
            metadata = {k: v for k, v in self.training_history.items() 
                       if k != 'shap_explainer'}
            json.dump(metadata, f, indent=2, ensure_ascii=False)
        print(f"✅ تم حفظ Metadata في: {metadata_path}")
    
    @classmethod
    def load(cls, model_path='models/saved/advanced_diabetes_model.pkl'):
        """تحميل نموذج محفوظ"""
        if not os.path.exists(model_path):
            raise FileNotFoundError(f"النموذج غير موجود: {model_path}")
        
        model_data = joblib.load(model_path)
        
        predictor = cls(
            model_type=model_data['model_type'],
            use_advanced_features=model_data['use_advanced_features']
        )
        predictor.model = model_data['model']
        predictor.preprocessing_pipeline = model_data['preprocessing_pipeline']
        predictor.feature_engineer = model_data.get('feature_engineer')
        predictor.feature_names = model_data['feature_names']
        predictor.training_history = model_data['training_history']
        predictor.shap_explainer = model_data.get('shap_explainer')
        
        print(f"✅ تم تحميل النموذج من: {model_path}")
        return predictor


# التشغيل الرئيسي
if __name__ == "__main__":
    print("="*80)
    print("🏥 Advanced Diabetes Prediction with SHAP")
    print("="*80)
    
    data_path = "../DataSet/diabetes_binary_5050split_health_indicators_BRFSS2015.csv"
    
    if not os.path.exists(data_path):
        print(f"❌ الملف غير موجود: {data_path}")
        exit(1)
    
    # تحميل البيانات
    print("\n📂 تحميل البيانات...")
    X_train, X_test, y_train, y_test = load_and_prepare_data(
        data_path, test_size=0.2, random_state=42
    )
    
    # استخدام عينة للاختبار السريع
    sample_size = min(10000, len(X_train))
    X_train = X_train.iloc[:sample_size]
    y_train = y_train.iloc[:sample_size]
    
    print(f"✅ Training: {X_train.shape}, Testing: {X_test.shape}")
    
    # تدريب النموذج
    model_type = 'xgboost' if XGBOOST_AVAILABLE else 'random_forest'
    predictor = AdvancedDiabetesPredictor(
        model_type=model_type,
        use_advanced_features=True
    )
    
    predictor.train(X_train, y_train, use_cv=True)
    
    # التقييم
    results = predictor.evaluate(X_test, y_test)
    
    # حفظ النموذج
    predictor.save()
    
    # اختبار SHAP
    print("\n" + "="*80)
    print("🔍 اختبار SHAP Explanation")
    print("="*80)
    
    sample_patient = X_test.iloc[0].to_dict()
    explanation = predictor.explain_prediction(sample_patient)
    
    print(f"\n✅ التنبؤ: {'سكري' if explanation['prediction'] == 1 else 'لا يوجد'}")
    print(f"✅ الاحتمال: {explanation['probability']:.2%}")
    
    if 'shap_explanation' in explanation:
        print(f"\n🔍 أهم العوامل المؤثرة:")
        for feat in explanation['shap_explanation']:
            if isinstance(feat, dict):
                print(f"   - {feat['feature']}: {feat['impact']} ({feat['shap_value']:.4f})")
    
    print("\n✅ اكتمل البرنامج بنجاح!")
