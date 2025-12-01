"""
Exploratory Data Analysis (EDA) - Diabetes Dataset
تحليل استكشافي شامل للبيانات
"""

import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
import seaborn as sns
from pathlib import Path
import warnings
warnings.filterwarnings('ignore')

# إعدادات الرسوم
plt.style.use('seaborn-v0_8-darkgrid')
sns.set_palette("husl")

print("="*80)
print("📊 Exploratory Data Analysis - BRFSS 2015 Diabetes Dataset")
print("="*80)

# تحميل البيانات
data_path = Path("../DataSet/diabetes_binary_health_indicators_BRFSS2015.csv")

if not data_path.exists():
    print(f"❌ الملف غير موجود: {data_path}")
    exit(1)

df = pd.read_csv(data_path)

print(f"\n✅ تم تحميل البيانات بنجاح")
print(f"   الحجم: {df.shape[0]:,} صف × {df.shape[1]} عمود")

# ==================== 1. نظرة عامة ====================
print("\n" + "="*80)
print("1️⃣ نظرة عامة على البيانات")
print("="*80)

print("\n📋 أول 5 صفوف:")
print(df.head())

print("\n📊 معلومات الأعمدة:")
print(df.info())

print("\n📈 إحصائيات وصفية:")
print(df.describe())

# ==================== 2. القيم المفقودة ====================
print("\n" + "="*80)
print("2️⃣ فحص القيم المفقودة")
print("="*80)

missing = df.isna().sum()
missing_pct = (missing / len(df)) * 100

missing_df = pd.DataFrame({
    'العمود': missing.index,
    'عدد المفقودة': missing.values,
    'النسبة %': missing_pct.values
})

print(missing_df[missing_df['عدد المفقودة'] > 0])

if missing.sum() == 0:
    print("✅ لا توجد قيم مفقودة!")

# ==================== 3. توزيع الهدف ====================
print("\n" + "="*80)
print("3️⃣ توزيع المتغير الهدف (Diabetes_binary)")
print("="*80)

target_counts = df['Diabetes_binary'].value_counts()
target_pct = df['Diabetes_binary'].value_counts(normalize=True) * 100

print(f"\n📊 التوزيع:")
print(f"   0 (لا يوجد سكري): {target_counts[0]:,} ({target_pct[0]:.2f}%)")
print(f"   1 (سكري/prediabetes): {target_counts[1]:,} ({target_pct[1]:.2f}%)")

imbalance_ratio = target_counts[0] / target_counts[1]
print(f"\n⚖️ نسبة عدم التوازن: {imbalance_ratio:.2f}:1")

if imbalance_ratio > 2:
    print("⚠️ البيانات غير متوازنة - يجب استخدام تقنيات balancing")

# ==================== 4. توزيع الميزات الرقمية ====================
print("\n" + "="*80)
print("4️⃣ توزيع الميزات الرقمية")
print("="*80)

numeric_cols = ['BMI', 'MentHlth', 'PhysHlth', 'Age', 'Education', 'Income']

for col in numeric_cols:
    if col in df.columns:
        print(f"\n📊 {col}:")
        print(f"   Min: {df[col].min():.2f}")
        print(f"   Max: {df[col].max():.2f}")
        print(f"   Mean: {df[col].mean():.2f}")
        print(f"   Median: {df[col].median():.2f}")
        print(f"   Std: {df[col].std():.2f}")

# ==================== 5. توزيع الميزات الثنائية ====================
print("\n" + "="*80)
print("5️⃣ توزيع الميزات الثنائية")
print("="*80)

binary_cols = ['HighBP', 'HighChol', 'Smoker', 'Stroke', 'HeartDiseaseorAttack',
               'PhysActivity', 'Fruits', 'Veggies', 'HvyAlcoholConsump',
               'AnyHealthcare', 'NoDocbcCost', 'DiffWalk', 'Sex']

for col in binary_cols:
    if col in df.columns:
        counts = df[col].value_counts()
        pct = (counts[1] / len(df)) * 100 if 1 in counts else 0
        print(f"   {col}: {pct:.1f}% = نعم")

# ==================== 6. الارتباطات ====================
print("\n" + "="*80)
print("6️⃣ الارتباطات مع المتغير الهدف")
print("="*80)

correlations = df.corr()['Diabetes_binary'].sort_values(ascending=False)
print("\n🔝 أعلى 10 ارتباطات:")
print(correlations.head(10))

print("\n🔻 أقل 10 ارتباطات:")
print(correlations.tail(10))

# ==================== 7. تحليل حسب الفئات ====================
print("\n" + "="*80)
print("7️⃣ تحليل BMI حسب حالة السكري")
print("="*80)

bmi_by_diabetes = df.groupby('Diabetes_binary')['BMI'].describe()
print(bmi_by_diabetes)

print("\n📊 متوسط BMI:")
print(f"   بدون سكري: {df[df['Diabetes_binary']==0]['BMI'].mean():.2f}")
print(f"   مع سكري: {df[df['Diabetes_binary']==1]['BMI'].mean():.2f}")

# ==================== 8. عوامل الخطر ====================
print("\n" + "="*80)
print("8️⃣ تحليل عوامل الخطر")
print("="*80)

risk_factors = ['HighBP', 'HighChol', 'BMI', 'Smoker', 'HeartDiseaseorAttack']

for factor in risk_factors:
    if factor in df.columns:
        if factor == 'BMI':
            high_bmi = (df['BMI'] > 30).sum()
            high_bmi_diabetes = ((df['BMI'] > 30) & (df['Diabetes_binary'] == 1)).sum()
            print(f"\n🔍 {factor} > 30:")
            print(f"   إجمالي: {high_bmi:,}")
            print(f"   مع سكري: {high_bmi_diabetes:,} ({high_bmi_diabetes/high_bmi*100:.1f}%)")
        else:
            with_factor = df[df[factor] == 1]
            diabetes_rate = (with_factor['Diabetes_binary'].sum() / len(with_factor)) * 100
            print(f"\n🔍 {factor}:")
            print(f"   معدل السكري عند وجوده: {diabetes_rate:.1f}%")

# ==================== 9. الفئات العمرية ====================
print("\n" + "="*80)
print("9️⃣ تحليل الفئات العمرية")
print("="*80)

age_diabetes = df.groupby('Age')['Diabetes_binary'].agg(['count', 'sum', 'mean'])
age_diabetes.columns = ['العدد', 'حالات السكري', 'النسبة']
age_diabetes['النسبة'] = age_diabetes['النسبة'] * 100

print(age_diabetes)

# ==================== 10. الخلاصة ====================
print("\n" + "="*80)
print("🎯 الخلاصة والتوصيات")
print("="*80)

print("\n✅ النتائج الرئيسية:")
print(f"   1. حجم البيانات: {len(df):,} سجل")
print(f"   2. عدم التوازن: {imbalance_ratio:.1f}:1")
print(f"   3. لا توجد قيم مفقودة")
print(f"   4. أهم عوامل الخطر: HighBP, HighChol, BMI, Age")

print("\n📋 التوصيات:")
print("   1. استخدام SMOTE أو class_weight للتعامل مع عدم التوازن")
print("   2. Feature Engineering: إنشاء مؤشرات مركبة")
print("   3. استخدام Stratified K-Fold للتقييم")
print("   4. تطبيع BMI, MentHlth, PhysHlth")

print("\n" + "="*80)
print("✅ اكتمل التحليل الاستكشافي!")
print("="*80)

# حفظ النتائج
output_dir = Path("../evaluation")
output_dir.mkdir(exist_ok=True)

summary = {
    'total_records': len(df),
    'features': df.shape[1],
    'missing_values': missing.sum(),
    'class_0': int(target_counts[0]),
    'class_1': int(target_counts[1]),
    'imbalance_ratio': float(imbalance_ratio),
    'top_correlations': correlations.head(5).to_dict()
}

import json
with open(output_dir / 'eda_summary.json', 'w', encoding='utf-8') as f:
    json.dump(summary, f, indent=2, ensure_ascii=False)

print(f"\n💾 تم حفظ الملخص في: {output_dir / 'eda_summary.json'}")
