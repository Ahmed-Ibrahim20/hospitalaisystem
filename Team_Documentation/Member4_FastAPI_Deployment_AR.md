# عضو الفريق 4 — خدمة FastAPI والنشر (AR)

## نطاق العمل

مسؤول عن بناء خدمة الـ API في بايثون (FastAPI)، تحديد الـ endpoints والعقود (API Contracts)، وتجهيز متطلبات النشر.

## ما الذي قمت بتنفيذه؟

-   إنشاء FastAPI service للتنبؤ بالسكري.
-   بناء Pydantic models للتحقق الصارم من البيانات:
    -   حدود القيم (BMI, Age group, binary flags)
    -   تقليل أخطاء الإدخال قبل وصولها للنموذج
-   تنفيذ الـ Endpoints الأساسية:
    -   `GET /health`
    -   `POST /predict` (مع/بدون SHAP)
    -   `POST /predict/batch`
    -   `GET /model/info`
    -   `GET /monitoring/report`
-   تفعيل CORS لسهولة التكامل مع Laravel.
-   تجهيز ملفات المتطلبات وهيكل النشر.

## أماكن الشغل داخل المشروع

-   الخدمة الأساسية:
    -   `AI-Powered/deployment/fastapi_service_advanced.py`
-   ملفات النشر والمتطلبات:
    -   `AI-Powered/deployment/`
    -   `AI-Powered/requirements.txt`

## التسليمات (Deliverables)

-   API جاهز للاستخدام مع Validation قوي
-   Endpoints للمراقبة وصحة الخدمة
-   أساس قابل للتوسع لدعم أمراض متعددة لاحقاً

## نقاط شرح في المناقشة

-   أهمية الـ validation في الأنظمة الطبية
-   الحفاظ على الأداء مع وجود SHAP
-   كيف يمكن توسيع الخدمة لأمراض أخرى بسهولة
