# عضو الفريق 2 — تطوير النماذج وتقييم الأداء (AR)

## نطاق العمل

مسؤول عن بناء نماذج التنبؤ بالسكري، مقارنة الخوارزميات، تحسين الأداء، وتجهيز النماذج لتكون جاهزة للنشر داخل خدمة الـ API.

## ما الذي قمت بتنفيذه؟

-   تدريب ومقارنة عدة نماذج Machine Learning:
    -   Logistic Regression (Baseline)
    -   Decision Tree (Baseline)
    -   Random Forest
    -   Gradient Boosting
    -   XGBoost
    -   LightGBM
-   تطبيق تقنيات Ensemble:
    -   Voting (Soft Voting)
    -   Stacking (Meta-learner: Logistic Regression)
-   ضبط Hyperparameters للوصول إلى أداء مستهدف مناسب للاستخدام كأداة مساعدة في التشخيص.
-   توثيق النتائج بالمقاييس الأساسية:
    -   Accuracy (≥ 87%)
    -   ROC-AUC (≥ 0.87)

## أماكن الشغل داخل المشروع

-   النماذج وحفظها:
    -   `AI-Powered/models/`
-   ملخص النماذج والأداء:
    -   `AI-Powered/COMPLETE_SYSTEM_GUIDE.md`

## التسليمات (Deliverables)

-   نماذج مدربة وجاهزة للاستخدام داخل FastAPI
-   مبررات اختيار النموذج الأفضل (Model Selection Rationale)
-   دعم توافق الـ Feature Order بين التدريب والـ Inference

## نقاط شرح في المناقشة

-   لماذا نماذج الـ Boosting مناسبة لبيانات Tabular الطبية
-   كيف يحسن الـ Ensemble الاستقرار وتقليل التحيز
-   معنى ROC-AUC في سيناريو Screening داخل المستشفى
