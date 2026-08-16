<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            // ربط الملف الشخصي بالمستخدم وحذفه تلقائياً في حال حذف المستخدم
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('organization_name')->nullable(); // اسم الفندق أو الجمعية
            $table->string('phone')->nullable();
            $table->text('address')->nullable();

            // تخزين الموقع الجغرافي بدقة (خطوط الطول والعرض) لفلترة المسافات
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // حقول اختيارية للتوثيق والتحقق من الحساب
            $table->string('license_number')->nullable(); // رقم الترخيص الحكومي
            $table->string('document_path')->nullable(); // رابط المستند المرفوع للتحقق
            $table->timestamp('verified_at')->nullable(); // وقت موافقة الإدارة على الحساب

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
