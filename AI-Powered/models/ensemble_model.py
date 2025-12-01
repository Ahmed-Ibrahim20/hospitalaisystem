"""
Ensemble Model with Stacking
نموذج Ensemble متقدم مع Stacking
"""

import pandas as pd
import numpy as np
from sklearn.ensemble import StackingClassifier, VotingClassifier
from sklearn.linear_model import LogisticRegression
from sklearn.ensemble import RandomForestClassifier, GradientBoostingClassifier
from sklearn.model_selection import cross_val_score, StratifiedKFold
import joblib
import os

try:
    import xgboost as xgb
    XGBOOST_AVAILABLE = True
except:
    XGBOOST_AVAILABLE = False

try:
    import lightgbm as lgb
    LIGHTGBM_AVAILABLE = True
except:
    LIGHTGBM_AVAILABLE = False

from preprocessing import load_and_prepare_data, create_preprocessing_pipeline
from advanced_features import MedicalFeatureEngineer


class EnsembleDiabetesPredictor:
    """
    نموذج Ensemble يجمع عدة نماذج للحصول على أفضل أداء
    """
    
    def __init__(self, ensemble_type='stacking'):
        """
        Parameters:
        -----------
        ensemble_type : str
            'voting' أو 'stacking'
        """
        self.ensemble_type = ensemble_type
        self.model = None
        self.preprocessing_pipeline = None
        self.feature_engineer = None
        
    def _create_base_models(self):
        """إنشاء النماذج الأساسية"""
        models = []
        
        # 1. Random Forest
        models.append(('rf', RandomForestClassifier(
            n_estimators=200,
            max_depth=15,
            class_weight='balanced',
            random_state=42,
            n_jobs=-1
        )))
        
        # 2. Gradient Boosting
        models.append(('gb', GradientBoostingClassifier(
            n_estimators=200,
            max_depth=5,
            learning_rate=0.05,
            random_state=42
        )))
        
        # 3. XGBoost
        if XGBOOST_AVAILABLE:
            models.append(('xgb', xgb.XGBClassifier(
                n_estimators=200,
                max_depth=6,
                learning_rate=0.05,
                scale_pos_weight=5,
                random_state=42,
                n_jobs=-1
            )))
        
        # 4. LightGBM
        if LIGHTGBM_AVAILABLE:
            models.append(('lgb', lgb.LGBMClassifier(
                n_estimators=200,
                max_depth=6,
                learning_rate=0.05,
                class_weight='balanced',
                random_state=42,
                n_jobs=-1,
                verbose=-1
            )))
        
        return models
    
    def _create_ensemble(self):
        """إنشاء Ensemble Model"""
        base_models = self._create_base_models()
        
        if self.ensemble_type == 'voting':
            # Voting Classifier
            return VotingClassifier(
                estimators=base_models,
                voting='soft',
                n_jobs=-1
            )
        else:
            # Stacking Classifier
            return StackingClassifier(
                estimators=base_models,
                final_estimator=LogisticRegression(
                    max_iter=1000,
                    class_weight='balanced',
                    random_state=42
                ),
                cv=5,
                n_jobs=-1
            )
    
    def train(self, X_train, y_train):
        """تدريب Ensemble"""
        print("\n" + "="*80)
        print(f"🚀 تدريب {self.ensemble_type.upper()} Ensemble")
        print("="*80)
        
        # Preprocessing
        print("\n1️⃣ معالجة البيانات...")
        self.preprocessing_pipeline = create_preprocessing_pipeline(scale_features=False)
        X_train_processed = self.preprocessing_pipeline.fit_transform(X_train)
        
        # Advanced Features
        print("2️⃣ إنشاء ميزات متقدمة...")
        self.feature_engineer = MedicalFeatureEngineer()
        X_train_processed = self.feature_engineer.fit_transform(
            pd.DataFrame(X_train_processed, columns=X_train.columns)
        )
        
        if isinstance(X_train_processed, pd.DataFrame):
            X_train_processed = X_train_processed.values
        
        print(f"   ✅ عدد الميزات: {X_train_processed.shape[1]}")
        
        # إنشاء Ensemble
        print(f"\n3️⃣ إنشاء {self.ensemble_type} ensemble...")
        self.model = self._create_ensemble()
        
        # Cross-Validation
        print("\n4️⃣ Cross-Validation...")
        cv = StratifiedKFold(n_splits=5, shuffle=True, random_state=42)
        cv_scores = cross_val_score(
            self.model, X_train_processed, y_train,
            cv=cv, scoring='roc_auc', n_jobs=-1
        )
        
        print(f"   CV ROC-AUC: {cv_scores.mean():.4f} (+/- {cv_scores.std():.4f})")
        
        # التدريب النهائي
        print("\n5️⃣ التدريب النهائي...")
        self.model.fit(X_train_processed, y_train)
        
        print("\n✅ اكتمل التدريب!")
    
    def predict(self, X):
        """التنبؤ"""
        X_processed = self.preprocessing_pipeline.transform(X)
        X_processed = self.feature_engineer.transform(
            pd.DataFrame(X_processed, columns=X.columns)
        )
        if isinstance(X_processed, pd.DataFrame):
            X_processed = X_processed.values
        
        return self.model.predict(X_processed)
    
    def predict_proba(self, X):
        """احتمالات التنبؤ"""
        X_processed = self.preprocessing_pipeline.transform(X)
        X_processed = self.feature_engineer.transform(
            pd.DataFrame(X_processed, columns=X.columns)
        )
        if isinstance(X_processed, pd.DataFrame):
            X_processed = X_processed.values
        
        return self.model.predict_proba(X_processed)
    
    def save(self, path='models/saved/ensemble_model.pkl'):
        """حفظ النموذج"""
        os.makedirs(os.path.dirname(path), exist_ok=True)
        
        model_data = {
            'model': self.model,
            'preprocessing_pipeline': self.preprocessing_pipeline,
            'feature_engineer': self.feature_engineer,
            'ensemble_type': self.ensemble_type
        }
        
        joblib.dump(model_data, path)
        print(f"✅ تم حفظ Ensemble في: {path}")


if __name__ == "__main__":
    print("="*80)
    print("🎯 Ensemble Model Training")
    print("="*80)
    
    data_path = "../DataSet/diabetes_binary_5050split_health_indicators_BRFSS2015.csv"
    
    if os.path.exists(data_path):
        X_train, X_test, y_train, y_test = load_and_prepare_data(
            data_path, test_size=0.2, random_state=42
        )
        
        # عينة للاختبار السريع
        X_train = X_train.iloc[:5000]
        y_train = y_train.iloc[:5000]
        
        # تدريب
        ensemble = EnsembleDiabetesPredictor(ensemble_type='stacking')
        ensemble.train(X_train, y_train)
        
        # تقييم
        from sklearn.metrics import roc_auc_score
        y_proba = ensemble.predict_proba(X_test)[:, 1]
        score = roc_auc_score(y_test, y_proba)
        
        print(f"\n📊 Test ROC-AUC: {score:.4f}")
        
        # حفظ
        ensemble.save()
        
        print("\n✅ اكتمل!")
