<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('disease_predictions', function (Blueprint $table) {
            // Drop existing recommendations column if it exists
            if (Schema::hasColumn('disease_predictions', 'recommendations')) {
                $table->dropColumn('recommendations');
            }

            // Multi-disease prediction support
            $table->string('disease_type')->default('diabetes')->comment('نوع المرض');
            $table->json('prediction_data')->nullable()->comment('بيانات التنبؤ كاملة');
            $table->json('risk_factors')->nullable()->comment('عوامل الخطر');
            $table->json('shap_values')->nullable()->comment('SHAP values للتفسير');
            $table->decimal('confidence_score', 5, 4)->nullable()->comment('مستوى الثقة');
            $table->string('model_version')->nullable()->comment('إصدار النموذج');
            $table->json('recommendations')->nullable()->comment('التوصيات الطبية');
            $table->text('doctor_notes')->nullable()->comment('ملاحظات الطبيب');
            $table->enum('status', ['pending', 'reviewed', 'confirmed', 'rejected'])->default('pending')->comment('حالة التنبؤ');
            $table->timestamp('reviewed_at')->nullable()->comment('تاريخ المراجعة');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->comment('الطبيب الذي راجع');

            // Add indexes
            $table->index(['disease_type', 'created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('disease_predictions', function (Blueprint $table) {
            $table->dropColumn([
                'disease_type',
                'prediction_data',
                'risk_factors',
                'shap_values',
                'confidence_score',
                'model_version',
                'recommendations',
                'doctor_notes',
                'status',
                'reviewed_at',
                'reviewed_by'
            ]);

            $table->dropIndex(['disease_type', 'created_at']);
            $table->dropIndex(['status', 'created_at']);
        });
    }
};
