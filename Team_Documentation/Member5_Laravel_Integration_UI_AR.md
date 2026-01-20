# عضو الفريق 5 — Laravel Backend والتكامل والواجهات (AR)

## نطاق العمل

مسؤول عن بناء نظام المستشفى على Laravel، وتكامل النظام مع خدمة FastAPI، وتجهيز واجهات الاستخدام ومسارات العمل داخل المستشفى.

## ما الذي قمت بتنفيذه؟

-   بناء Laravel 12 بهيكل:
    -   MVC + Service Layer
    -   MySQL + migrations
    -   Authentication باستخدام Breeze + صلاحيات (Role-based access)
-   تنفيذ كيانات وسيناريوهات المستشفى:
    -   Patients و Encounters/Visits و Disease Predictions
    -   تخزين نتيجة التنبؤ (Probability, Risk Level, SHAP, Recommendations)
-   تنفيذ طبقة التكامل مع FastAPI:
    -   `app/Services/AIPredictionService.php`
    -   طلبات HTTP باستخدام Guzzle
    -   Health check و model info
    -   Batch prediction
    -   Fallback لنتائج تجريبية لضمان استمرارية النظام عند تعطل الـ API
-   ربط الواجهات والمسارات:
    -   AI dashboard
    -   صفحات التنبؤ وعرض النتائج
    -   مراجعة الطبيب للنتائج (Confirmed/Rejected)
    -   تقارير وإحصائيات

## أماكن الشغل داخل المشروع

-   خدمة التكامل:
    -   `app/Services/AIPredictionService.php`
-   Routes:
    -   `routes/web.php` (AI تحت prefix `ai/`)
-   Views:
    -   `resources/views/`

## التسليمات (Deliverables)

-   تدفق كامل: Encounter → Prediction API → Database → Doctor Review
-   صلاحيات مناسبة للبيئة الطبية (حوكمة وإدارة)

## نقاط شرح في المناقشة

-   كيف عالج النظام الفجوة بين نموذج بحثي وبين Hospital System حقيقي
-   لماذا تخزين النتائج مهم للمراجعة والتتبع (Audit)
-   دور الصلاحيات في حماية النظام ومسارات العمل
