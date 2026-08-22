<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewListingNotification;

class ListingApiController extends Controller
{
    /**
     * استقبال ومعالجة وحفظ طلبات التبرع التلقائية الواردة من كاشير الفنادق (POS API) [4, 8]
     */
    public function store(Request $request, AIService $aiService)
    {
        $user = $request->user();

        // حظر أمني: التأكد من أن صاحب الرمز المتصل هو متبرع مرخص ونشط بالكامل [8]
        if ($user->role !== 'donor' || $user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'عذراً، هذا الحساب غير مصرح له بنشر التبرعات حالياً.'
            ], 403);
        }

        // التحقق من بيانات حمولة الـ JSON الواردة من الكاشير بدقة
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'in:cooked,dry,fresh'],
            'expiry_time' => ['required', 'date', 'after:now'],
            'address' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        // صياغة الوصف ومسببات الحساسية تلقائياً بالذكاء الاصطناعي لتبسيط التكامل على مبرمجي الفندق [4]
        $aiResult = $aiService->generateDescriptionAndAllergens($request->title);
        $description = ($aiResult['description'] ?? 'فائض طعام مجهز وموثق تلقائياً.') . "\n\n⚠️ " . ($aiResult['allergens'] ?? '');

        // حفظ المعاملة ونشر الوجبة في المنصة فوراً
        $listing = $user->listings()->create([
            'title' => $request->title,
            'description' => $description,
            'quantity' => $request->quantity,
            'category' => $request->category,
            'expiry_time' => $request->expiry_time,
            // في حال عدم إرسال عنوان محدد، نأخذ تلقائياً إحداثيات وعنوان ملف المتبرع الافتراضية
            'address' => $request->address ?? $user->profile->address ?? 'مقر الفندق الرئيسي',
            'latitude' => $request->latitude ?? $user->profile->latitude ?? 31.5000,
            'longitude' => $request->longitude ?? $user->profile->longitude ?? 34.4667,
        ]);

        // إرسال الإشعارات اللحظية الحية لجميع الجمعيات لإنقاذ الوجبة فوراً
        $allCharities = \App\Models\User::where('role', 'receiver')->get();
        if ($allCharities->isNotEmpty()) {
            Notification::send($allCharities, new NewListingNotification($listing));
        }

        // إرجاع كود الاستجابة المكتملة 211 بنجاح لنظام الكاشير الخارجي
        return response()->json([
            'success' => true,
            'message' => 'تم استلام وتوثيق إعلان الفائض من نظام الكاشير ونشره حياً بنجاح!',
            'listing_id' => $listing->id,
            'title' => $listing->title,
        ], 201);
    }
}