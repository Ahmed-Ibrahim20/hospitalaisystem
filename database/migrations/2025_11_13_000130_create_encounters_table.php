<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encounters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users');
            $table->dateTime('visit_date');

            // AI Medical Data - البيانات الطبية للتنبؤ
            $table->tinyInteger('high_bp')->nullable()->comment('ضغط دم مرتفع (0=لا, 1=نعم)');
            $table->tinyInteger('high_chol')->nullable()->comment('كوليسترول عالي (0=لا, 1=نعم)');
            $table->tinyInteger('chol_check')->nullable()->comment('فحص كوليسترول (0=لا, 1=نعم)');
            $table->decimal('bmi', 5, 2)->nullable()->comment('مؤشر كتلة الجسم');
            $table->tinyInteger('smoker')->nullable()->comment('مدخن (0=لا, 1=نعم)');
            $table->tinyInteger('stroke')->nullable()->comment('سكتة دماغية (0=لا, 1=نعم)');
            $table->tinyInteger('heart_disease')->nullable()->comment('مرض قلبي (0=لا, 1=نعم)');
            $table->tinyInteger('phys_activity')->nullable()->comment('نشاط بدني (0=لا, 1=نعم)');
            $table->tinyInteger('fruits')->nullable()->comment('تناول فواكه (0=لا, 1=نعم)');
            $table->tinyInteger('veggies')->nullable()->comment('تناول خضار (0=لا, 1=نعم)');
            $table->tinyInteger('heavy_alcohol')->nullable()->comment('استهلاك كحول عالي (0=لا, 1=نعم)');
            $table->tinyInteger('any_healthcare')->nullable()->comment('وجود تأمين صحي (0=لا, 1=نعم)');
            $table->tinyInteger('no_doc_cost')->nullable()->comment('عدم زيارة طبيب بسبب التكلفة (0=لا, 1=نعم)');
            $table->tinyInteger('gen_health')->nullable()->comment('الصحة العامة (1=ممتاز, 5=سيء)');
            $table->tinyInteger('ment_health')->nullable()->comment('أيام صحة نفسية سيئة (0-30)');
            $table->tinyInteger('phys_health')->nullable()->comment('أيام صحة جسدية سيئة (0-30)');
            $table->tinyInteger('diff_walking')->nullable()->comment('صعوبة في المشي (0=لا, 1=نعم)');
            $table->tinyInteger('sex')->nullable()->comment('الجنس (0=أنثى, 1=ذكر)');
            $table->tinyInteger('age_group')->nullable()->comment('الفئة العمرية (1-13)');
            $table->tinyInteger('education')->nullable()->comment('المستوى التعليمي (1-6)');
            $table->tinyInteger('income')->nullable()->comment('مستوى الدخل (1-8)');

            // Additional Medical Data - بيانات طبية إضافية
            $table->decimal('blood_pressure_systolic', 5, 2)->nullable()->comment('الضغط الانقباضي');
            $table->decimal('blood_pressure_diastolic', 5, 2)->nullable()->comment('الضغط الانبساطي');
            $table->decimal('blood_sugar_fasting', 5, 2)->nullable()->comment('سكر الدم صائماً');
            $table->decimal('blood_sugar_random', 5, 2)->nullable()->comment('سكر الدم عشوائي');
            $table->decimal('weight', 5, 2)->nullable()->comment('الوزن (كجم)');
            $table->decimal('height', 5, 2)->nullable()->comment('الطول (سم)');
            $table->text('symptoms')->nullable()->comment('الأعراض المبلغ عنها');
            $table->text('medications')->nullable()->comment('الأدوية الحالية');
            $table->text('allergies')->nullable()->comment('الحساسية');
            $table->text('family_history')->nullable()->comment('التاريخ المرضي العائلي');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encounters');
    }
};
