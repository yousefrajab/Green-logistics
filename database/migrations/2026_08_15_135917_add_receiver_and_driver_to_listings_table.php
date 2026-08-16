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
        Schema::table('listings', function (Blueprint $table) {
            // حقل يربط الإعلان بالجمعية المستلمة (حق اختياري يكون فارغاً في البداية)
            $table->foreignId('receiver_id')->nullable()->constrained('users')->nullOnDelete();

            // حقل يربط الإعلان بالمندوب الموصل (حق اختياري يكون فارغاً في البداية)
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropForeign(['receiver_id']);
            $table->dropForeign(['driver_id']);
            $table->dropColumn(['receiver_id', 'driver_id']);
        });
    }
};
