<?php

use Illuminate\Support\Facades\Schedule;
use App\Models\Listing;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Console Routes & Task Scheduler (جدولة المهام البرمجية لـ جود)
|--------------------------------------------------------------------------
*/

// مهمة مجدولة تعمل في الخلفية كل دقيقة لحماية سلامة الغذاء [6]
Schedule::call(function () {
    // البحث عن جميع الإعلانات المعروضة كمتاحة وتجاوزت وقت انتهائها وتحديث حالتها
    $expiredCount = Listing::where('status', 'available')
        ->where('expiry_time', '<', Carbon::now())
        ->update(['status' => 'expired']);

    if ($expiredCount > 0) {
        logger()->info("Joud Scheduler: Auto-expired {$expiredCount} food listings successfully.");
    }
})->everyMinute(); // تكرار الفحص التلقائي كل دقيقة