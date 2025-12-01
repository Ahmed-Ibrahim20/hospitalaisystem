# Data Directory - BRFSS 2015 Diabetes Dataset

## نظرة عامة
هذا المجلد يحتوي على بيانات BRFSS 2015 الخاصة بمؤشرات السكري والصحة.

## الملفات المتاحة

### 1. diabetes_binary_health_indicators_BRFSS2015.csv
- **الهدف**: Binary classification (0 = no diabetes, 1 = prediabetes/diabetes)
- **الاستخدام**: النموذج الأساسي للتنبؤ بوجود/عدم وجود السكري
- **عدد الصفوف**: ~253,680
- **التوازن**: غير متوازن (imbalanced)

### 2. diabetes_binary_5050split_health_indicators_BRFSS2015.csv
- **الهدف**: Binary classification (0/1)
- **الاستخدام**: نسخة متوازنة 50/50 - مفيدة للتدريب الأولي
- **الميزة**: يقلل من تأثير class imbalance

### 3. diabetes_012_health_indicators_BRFSS2015.csv
- **الهدف**: Multi-class classification (0 = no, 1 = prediabetes, 2 = diabetes)
- **الاستخدام**: تمييز مراحل المرض
- **الميزة**: يوفر تفاصيل أكثر عن حالة المريض

## قاموس البيانات (Data Dictionary)

| العمود | النوع | الوصف | القيم |
|--------|------|-------|-------|
| **Diabetes_binary** | Target | وجود السكري | 0=لا, 1=نعم |
| **Diabetes_012** | Target | مرحلة السكري | 0=لا, 1=prediabetes, 2=diabetes |
| **HighBP** | Binary | ضغط دم مرتفع | 0=لا, 1=نعم |
| **HighChol** | Binary | كوليسترول عالي | 0=لا, 1=نعم |
| **CholCheck** | Binary | فحص كوليسترول في آخر 5 سنوات | 0=لا, 1=نعم |
| **BMI** | Numeric | مؤشر كتلة الجسم | قيمة عددية |
| **Smoker** | Binary | مدخن (100+ سيجارة) | 0=لا, 1=نعم |
| **Stroke** | Binary | سكتة دماغية سابقة | 0=لا, 1=نعم |
| **HeartDiseaseorAttack** | Binary | مرض قلبي/نوبة قلبية | 0=لا, 1=نعم |
| **PhysActivity** | Binary | نشاط بدني (آخر 30 يوم) | 0=لا, 1=نعم |
| **Fruits** | Binary | تناول فواكه يومياً | 0=لا, 1=نعم |
| **Veggies** | Binary | تناول خضار يومياً | 0=لا, 1=نعم |
| **HvyAlcoholConsump** | Binary | استهلاك كحول عالي | 0=لا, 1=نعم |
| **AnyHealthcare** | Binary | وجود تأمين صحي | 0=لا, 1=نعم |
| **NoDocbcCost** | Binary | عدم زيارة طبيب بسبب التكلفة | 0=لا, 1=نعم |
| **GenHlth** | Ordinal | الصحة العامة | 1=ممتاز, 5=سيء |
| **MentHlth** | Numeric | أيام صحة نفسية سيئة (آخر 30 يوم) | 0-30 |
| **PhysHlth** | Numeric | أيام صحة جسدية سيئة (آخر 30 يوم) | 0-30 |
| **DiffWalk** | Binary | صعوبة في المشي | 0=لا, 1=نعم |
| **Sex** | Binary | الجنس | 0=أنثى, 1=ذكر |
| **Age** | Categorical | الفئة العمرية | 1-13 (فئات) |
| **Education** | Ordinal | المستوى التعليمي | 1-6 |
| **Income** | Ordinal | مستوى الدخل | 1-8 |

## ملاحظات مهمة

### القيم المفقودة
- البيانات نظيفة ولا تحتوي على قيم مفقودة (NaN)
- جميع القيم رقمية (float)

### التوازن (Class Balance)
- `diabetes_binary`: غير متوازن (~85% negative, ~15% positive)
- `diabetes_binary_5050split`: متوازن (50% لكل فئة)
- `diabetes_012`: غير متوازن

### توصيات الاستخدام
1. **للتدريب الأولي**: استخدم `diabetes_binary_5050split`
2. **للتقييم النهائي**: استخدم `diabetes_binary` (يعكس التوزيع الحقيقي)
3. **لتمييز المراحل**: استخدم `diabetes_012`

## المصدر
Behavioral Risk Factor Surveillance System (BRFSS) 2015
- مسح صحي سنوي من CDC
- بيانات عامة ومتاحة للاستخدام البحثي
