<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\AdminController; // استدعاء متحكم الإدارة الجديد
use App\Models\Listing;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

// 1. تحويل الصفحة الرئيسية تلقائياً لصفحة جود الزمردية لتسجيل الدخول
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. مسار لوحة التحكم الرئيسية المطور بالإحصائيات الحقيقية والديناميكية بالكامل
Route::get('/dashboard', function () {
    $user = Auth::user();

    // حساب أرقام الوجبات المكتملة والنشطة في قاعدة البيانات للعدادات العلوية
    $completedCount = Listing::where('status', 'completed')->count();
    $completeCount =  Listing::where('status', 'completed')->sum('quantity');
    $activeCount = Listing::whereIn('status', ['reserved', 'picked_up'])->count();
    $totalCount = $completedCount + $activeCount;

    // حساب معدل تلبية الطلبات بالنسبة المئوية
    $fulfillmentRate = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;

    // حساب انحناء رسمة الـ SVG الدائرية برمجياً بناءً على النسبة المئوية الحقيقية
    $dashoffset = 376.8 - (376.8 * $fulfillmentRate / 100);

    // إحصائيات الأيام الـ 7 الأخيرة للأعمدة البيانية (بشكل متوافق مع كافة قواعد البيانات)
    $weeklyStats = [
        'الأحد' => 0,
        'الإثنين' => 0,
        'الثلاثاء' => 0,
        'الأربعاء' => 0,
        'الخميس' => 0,
        'الجمعة' => 0,
        'السبت' => 0
    ];

    // جلب الإعلانات المكتملة في آخر 7 أيام
    $completedListings = Listing::where('status', 'completed')
        ->where('updated_at', '>=', Carbon::now()->subDays(7))
        ->get();

    // خريطة تحويل أرقام الأيام من لغة الكربون إلى اللغة العربية
    $dayMapping = [
        0 => 'الأحد',
        1 => 'الإثنين',
        2 => 'الثلاثاء',
        3 => 'الأربعاء',
        4 => 'الخميس',
        5 => 'الجمعة',
        6 => 'السبت'
    ];

    foreach ($completedListings as $listing) {
        $dayOfWeek = $listing->updated_at->dayOfWeek; // يعيد رقماً من 0 (الأحد) إلى 6 (السبت)
        $arabicDay = $dayMapping[$dayOfWeek];
        $weeklyStats[$arabicDay]++;
    }

    // حساب الارتفاع المئوي للأعمدة برمجياً لكي لا تتجاوز المساحة المخصصة لها
    $maxCount = max(array_values($weeklyStats)) ?: 1;
    $weeklyHeights = [];
    foreach ($weeklyStats as $day => $count) {
        $weeklyHeights[$day] = round(($count / $maxCount) * 100);
    }

    return view('dashboard', compact(
        'completedCount',
        'activeCount',
        'fulfillmentRate',
        'dashoffset',
        'weeklyStats',
        'weeklyHeights',
        'completeCount'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

// 3. مسارات الأعضاء المسجلين والمحمية بالـ Auth Middleware
Route::middleware('auth')->group(function () {

    // مسارات الملف الشخصي
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // مسارات إعلانات الفائض (إنشاء، تعديل، تحديث، حذف، تراجع)
    Route::get('/listings/create', [ListingController::class, 'create'])->name('listings.create');
    Route::post('/listings', [ListingController::class, 'store'])->name('listings.store');
    Route::get('/listings/{listing}/edit', [ListingController::class, 'edit'])->name('listings.edit');
    Route::put('/listings/{listing}', [ListingController::class, 'update'])->name('listings.update');
    Route::delete('/listings/{listing}', [ListingController::class, 'destroy'])->name('listings.destroy');
    Route::post('/listings/{listing}/cancel', [ListingController::class, 'cancel'])->name('listings.cancel');

    // مسار حجز الوجبة للجمعيات الخيرية
    Route::post('/listings/{listing}/reserve', [ListingController::class, 'reserve'])->name('listings.reserve');

    // مسارات المندوب والتوصيل اللوجستي (قبول، استلام، تسليم مكتمل)
    Route::post('/listings/{listing}/accept-delivery', [ListingController::class, 'acceptDelivery'])->name('listings.accept-delivery');
    Route::post('/listings/{listing}/pickup-delivery', [ListingController::class, 'pickupDelivery'])->name('listings.pickup-delivery');
    Route::post('/listings/{listing}/complete-delivery', [ListingController::class, 'completeDelivery'])->name('listings.complete-delivery');

    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::get('/listings/{listing}/receipt', [ListingController::class, 'receipt'])->name('listings.receipt');


    Route::post('/api/listings/analyze-image', [ListingController::class, 'analyzeImage'])->name('api.listings.analyze-image');

    // مسار الـ API لجلب وتوليد تقارير الأثر البيئي بالذكاء الاصطناعي للمتبرع
    Route::post('/listings/green-report', [ListingController::class, 'generateGreenReport'])->name('donor.green-report');

    Route::get('/listings/green-report/print', [ListingController::class, 'printGreenReport'])->name('donor.green-report.print');

    Route::post('/donor/tokens', [ListingController::class, 'generateToken'])->name('donor.tokens.generate');
    // مسار الـ API لجلب الحالة اللحظية للوجبة والمندوب بشكل حي في الخلفية [2]
    Route::get('/api/listings/{listing}/status', function (\App\Models\Listing $listing) {
        return response()->json([
            'status' => $listing->status,
            'driver_id' => $listing->driver_id,
            'driver_name' => $listing->driver?->name ?? null,
            'driver_phone' => $listing->driver?->profile?->phone ?? null,
        ]);
    })->middleware('auth');

    // مسارات التنبيهات والإشعارات اللحظية والـ API في جرس الإشعارات المطور
    Route::get('/notifications/read-all', function () {
        Auth::user()->unreadNotifications->markAsRead();
        return redirect()->back();
    })->name('notifications.read-all');

    Route::get('/notifications/{id}/read', function ($id) {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $listingId = $notification->data['listing_id'] ?? null;

        if ($listingId) {
            return redirect()->to(route('dashboard') . '#listing-' . $listingId);
        }

        return redirect()->route('dashboard');
    })->name('notifications.read-single');

    Route::get('/api/notifications/unread-count', function () {
        $user = Auth::user();

        if (!$user) return response()->json(['count' => 0, 'notifications' => []]);

        return response()->json([
            'count' => $user->unreadNotifications->count(),
            'notifications' => $user->unreadNotifications()->take(5)->get()->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'message' => $notification->data['message'] ?? '',
                    'time' => $notification->created_at->diffForHumans()
                ];
            })
        ]);
    })->name('api.notifications.unread');

    // =========================================================
    // مسارات مدير النظام وحوكمة الحسابات (Approve, Reject, Update Status)
    // =========================================================
    Route::post('/admin/users/{user}/approve', [AdminController::class, 'approve'])->name('admin.users.approve');
    Route::post('/admin/users/{user}/reject', [AdminController::class, 'reject'])->name('admin.users.reject');
    Route::post('/admin/users/{user}/update-status', [AdminController::class, 'updateStatus'])->name('admin.users.update-status');
});

// مسار تسجيل الخروج المؤقت والسهل لمرحلة التطوير عبر GET [8]
Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
});

require __DIR__ . '/auth.php';
