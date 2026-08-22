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
        Schema::create('listing_items', function (Blueprint $table) {
            $table->id();
            // ربط الصنف بالشحنة الأم (Listing) وحذفه تلقائياً عند حذف الإعلان
            $table->foreignId('listing_id')->constrained('listings')->cascadeOnDelete();

            $table->string('title'); // اسم صنف الطعام (مثال: كبسة دجاج)
            $table->string('quantity'); // الكمية (مثال: صينية كبيرة)
            $table->string('category'); // التصنيف: cooked, dry, fresh
            $table->dateTime('expiry_time'); // وقت انتهاء الصلاحية
            $table->text('description')->nullable(); // مكونات الصنف وملاحظاته
            $table->string('image_path')->nullable(); // صورة مستقلة للصنف (اختياري)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_items');
    }
};
