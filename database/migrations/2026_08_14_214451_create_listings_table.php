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
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            // ربط الإعلان بالمتبرع (User) الذي قام بإنشائه
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('title'); // مثلاً: وجبات أرز ولحم فائضة
            $table->text('description')->nullable(); // تفاصيل المكونات أو طريقة الحفظ
            $table->string('quantity'); // الكمية (مثلاً: "20 وجبة" أو "15 كيلوغرام")
            $table->string('category'); // تصنيف: مطبوخ (Cooked)، جاف (Dry)، خضار وفواكه (Fresh)

            $table->dateTime('expiry_time'); // وقت انتهاء الصلاحية الحرج لسلامة الغذاء
            $table->string('status')->default('available'); // الحالات: available, reserved, picked_up, expired

            // حفظ موقع الإعلان الجغرافي ليسهل على الجمعيات فلترته
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('address')->nullable(); // العنوان النصي المكتوب

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
