"""
Model Monitoring and Performance Tracking
مراقبة أداء النموذج
"""

import pandas as pd
import numpy as np
from datetime import datetime
import json
import os
from pathlib import Path


class ModelMonitor:
    """
    مراقبة أداء النموذج في الإنتاج
    - Feature Drift Detection
    - Performance Tracking
    - Prediction Logging
    """
    
    def __init__(self, log_dir='logs/predictions'):
        self.log_dir = Path(log_dir)
        self.log_dir.mkdir(parents=True, exist_ok=True)
        
        self.predictions_log = []
        self.performance_metrics = []
        
    def log_prediction(self, input_data, prediction, probability, patient_id=None):
        """
        تسجيل التنبؤ
        
        Parameters:
        -----------
        input_data : dict
            بيانات المدخلات
        prediction : int
            التنبؤ
        probability : float
            الاحتمالية
        patient_id : str
            معرف المريض (اختياري - للتتبع فقط)
        """
        log_entry = {
            'timestamp': datetime.now().isoformat(),
            'patient_id': patient_id,
            'prediction': int(prediction),
            'probability': float(probability),
            'input_features': {
                k: float(v) if isinstance(v, (int, float, np.number)) else v
                for k, v in input_data.items()
            }
        }
        
        self.predictions_log.append(log_entry)
        
        # حفظ كل 100 تنبؤ
        if len(self.predictions_log) >= 100:
            self._save_predictions()
    
    def _save_predictions(self):
        """حفظ سجل التنبؤات"""
        if not self.predictions_log:
            return
        
        timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
        log_file = self.log_dir / f'predictions_{timestamp}.json'
        
        with open(log_file, 'w', encoding='utf-8') as f:
            json.dump(self.predictions_log, f, indent=2, ensure_ascii=False)
        
        print(f"💾 تم حفظ {len(self.predictions_log)} تنبؤ في: {log_file}")
        self.predictions_log = []
    
    def detect_feature_drift(self, current_data, reference_data, threshold=0.1):
        """
        كشف Feature Drift
        
        Parameters:
        -----------
        current_data : DataFrame
            البيانات الحالية
        reference_data : DataFrame
            البيانات المرجعية (من التدريب)
        threshold : float
            عتبة الانحراف
            
        Returns:
        --------
        dict : تقرير الانحراف
        """
        drift_report = {}
        
        for col in current_data.columns:
            if col in reference_data.columns:
                # حساب الفرق في المتوسط
                current_mean = current_data[col].mean()
                reference_mean = reference_data[col].mean()
                
                if reference_mean != 0:
                    drift = abs(current_mean - reference_mean) / abs(reference_mean)
                else:
                    drift = 0
                
                drift_report[col] = {
                    'current_mean': float(current_mean),
                    'reference_mean': float(reference_mean),
                    'drift': float(drift),
                    'drifted': drift > threshold
                }
        
        # عدد الميزات المنحرفة
        drifted_features = sum(1 for v in drift_report.values() if v['drifted'])
        
        return {
            'timestamp': datetime.now().isoformat(),
            'total_features': len(drift_report),
            'drifted_features': drifted_features,
            'drift_percentage': (drifted_features / len(drift_report)) * 100,
            'details': drift_report
        }
    
    def track_performance(self, y_true, y_pred, y_proba):
        """
        تتبع الأداء
        
        Parameters:
        -----------
        y_true : array
            القيم الحقيقية
        y_pred : array
            التنبؤات
        y_proba : array
            الاحتمالات
        """
        from sklearn.metrics import (
            accuracy_score, precision_score, recall_score,
            f1_score, roc_auc_score
        )
        
        metrics = {
            'timestamp': datetime.now().isoformat(),
            'accuracy': float(accuracy_score(y_true, y_pred)),
            'precision': float(precision_score(y_true, y_pred, zero_division=0)),
            'recall': float(recall_score(y_true, y_pred, zero_division=0)),
            'f1_score': float(f1_score(y_true, y_pred, zero_division=0)),
            'roc_auc': float(roc_auc_score(y_true, y_proba))
        }
        
        self.performance_metrics.append(metrics)
        
        return metrics
    
    def generate_report(self):
        """إنشاء تقرير شامل"""
        report = {
            'generated_at': datetime.now().isoformat(),
            'total_predictions': len(self.predictions_log),
            'performance_history': self.performance_metrics[-10:]  # آخر 10 قياسات
        }
        
        # حفظ التقرير
        report_file = self.log_dir / 'monitoring_report.json'
        with open(report_file, 'w', encoding='utf-8') as f:
            json.dump(report, f, indent=2, ensure_ascii=False)
        
        print(f"📊 تم إنشاء التقرير: {report_file}")
        
        return report
    
    def alert_if_degraded(self, current_metrics, baseline_metrics, threshold=0.05):
        """
        إنشاء تنبيه إذا انخفض الأداء
        
        Parameters:
        -----------
        current_metrics : dict
            المقاييس الحالية
        baseline_metrics : dict
            المقاييس الأساسية
        threshold : float
            عتبة الانخفاض
            
        Returns:
        --------
        bool : هل يوجد تدهور؟
        """
        degradation = {}
        
        for metric in ['accuracy', 'precision', 'recall', 'roc_auc']:
            if metric in current_metrics and metric in baseline_metrics:
                current = current_metrics[metric]
                baseline = baseline_metrics[metric]
                
                diff = baseline - current
                degradation[metric] = {
                    'current': current,
                    'baseline': baseline,
                    'difference': diff,
                    'degraded': diff > threshold
                }
        
        is_degraded = any(v['degraded'] for v in degradation.values())
        
        if is_degraded:
            print("\n⚠️ تحذير: انخفاض في أداء النموذج!")
            for metric, info in degradation.items():
                if info['degraded']:
                    print(f"   {metric}: {info['current']:.4f} (كان {info['baseline']:.4f})")
        
        return is_degraded


# مثال على الاستخدام
if __name__ == "__main__":
    print("="*80)
    print("📊 Model Monitoring System")
    print("="*80)
    
    monitor = ModelMonitor()
    
    # محاكاة تنبؤات
    for i in range(5):
        sample_input = {
            'HighBP': np.random.randint(0, 2),
            'BMI': np.random.uniform(20, 35),
            'Age': np.random.randint(1, 13)
        }
        
        prediction = np.random.randint(0, 2)
        probability = np.random.uniform(0.3, 0.9)
        
        monitor.log_prediction(sample_input, prediction, probability, f"patient_{i}")
    
    # إنشاء تقرير
    report = monitor.generate_report()
    
    print("\n✅ اكتمل الاختبار!")
