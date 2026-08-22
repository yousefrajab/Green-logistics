<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewListingNotification;

class ListingController extends Controller
{
    /**
     * عرض صفحة إضافة إعلان جديد
     */
    public function create()
    {
        $user = Auth::user();

        if ($user->role !== 'donor' || $user->status !== 'active') {
            return redirect()->route('dashboard')
                ->with('error', 'عذراً، يجب أن يكون حسابك نشطاً وبصلاحية متبرع لتتمكن من إضافة إعلانات فائض.');
        }

        return view('listings.create', compact('user'));
    }

    /**
     * حفظ الشحنة الكلية والأصناف الفرعية المتعددة في قاعدة البيانات
     */
    public function store(Request $request, AIService $aiService)
    {
        $user = Auth::user();

        if ($user->role !== 'donor' || $user->status !== 'active') {
            return redirect()->route('dashboard')->with('error', 'عملية غير مصرح بها.');
        }

        $request->validate([
            'items' => ['required', 'array', 'min:1'], // مصفوفة الأصناف مطلوبة وبها صنف واحد على الأقل
            'items.*.title' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'string', 'max:100'],
            'items.*.category' => ['required', 'string', 'in:cooked,dry,fresh'],
            'items.*.expiry_time' => ['required', 'date', 'after:now'],
            'items.*.description' => ['nullable', 'string', 'max:1000'],
            
            'address' => ['required', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'image' => ['nullable', 'image', 'max:3072'], // صورة الشحنة الكلية
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('listings', 'public');
        }

        // جلب بيانات الصنف الأول في المصفوفة لحفظها بالجدول الرئيسي لضمان التوافقية
        $firstItem = $request->items[0];

        // 1. إنشاء الشحنة الكبرى الأم (Listing)
        $listing = $user->listings()->create([
            'title' => $firstItem['title'],
            'description' => $firstItem['description'] ?? 'شحنة فائض طعام متعددة.',
            'quantity' => $firstItem['quantity'],
            'category' => $firstItem['category'],
            'expiry_time' => $firstItem['expiry_time'],
            'address' => $request->address,
            'latitude' => $request->latitude ?? $user->profile->latitude ?? 31.5000,
            'longitude' => $request->longitude ?? $user->profile->longitude ?? 34.4667,
            'image_path' => $imagePath,
        ]);

        // 2. تكرار وحفظ جميع الأصناف الفرعية بالكامل بجدول listing_items الجديد [8]
        foreach ($request->items as $item) {
            $listing->items()->create([
                'title' => $item['title'],
                'quantity' => $item['quantity'],
                'category' => $item['category'],
                'expiry_time' => $item['expiry_time'],
                'description' => $item['description'] ?? null,
            ]);
        }

        // 3. إرسال الإشعارات اللحظية لجميع الجمعيات لإنقاذ الشحنة
        $allCharities = \App\Models\User::where('role', 'receiver')->get();
        if ($allCharities->isNotEmpty()) {
            Notification::send($allCharities, new NewListingNotification($listing));
        }

        return redirect()->route('dashboard')
            ->with('success', 'تم نشر شحنة الفائض المتعددة بنجاح! تم حفظ كافة الأصناف في السلة وإشعار الجمعيات.');
    }

    /**
     * عرض صفحة تعديل الإعلان
     */
    public function edit(Listing $listing)
    {
        $user = Auth::user();

        if ($listing->user_id !== $user->id || $listing->status === 'completed') {
            return redirect()->route('dashboard')->with('error', 'غير مصرح لك بتعديل هذا الإعلان في حالته الحالية.');
        }

        // تحميل الأصناف الفرعية المرتبطة بالشحنة مسبقاً لتمريرها للتعديل
        $listing->load('items');

        return view('listings.edit', compact('listing', 'user'));
    }

    /**
     * تحديث الشحنة الكبرى وحذف وإعادة بناء الأصناف الفرعية المعدلة في قاعدة البيانات [8]
     */
    public function update(Request $request, Listing $listing)
    {
        $user = Auth::user();

        if ($listing->user_id !== $user->id || $listing->status === 'completed') {
            return redirect()->route('dashboard')->with('error', 'عملية غير مصرح بها.');
        }

        $request->validate([
            'items' => ['required', 'array', 'min:1'], // مصفوفة الأصناف مطلوبة بالتعديل أيضاً
            'items.*.title' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'string', 'max:100'],
            'items.*.category' => ['required', 'string', 'in:cooked,dry,fresh'],
            'items.*.expiry_time' => ['required', 'date', 'after:now'],
            'items.*.description' => ['nullable', 'string', 'max:1000'],
            
            'address' => ['required', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'image' => ['nullable', 'image', 'max:3072'], // تعديل صورة الشحنة الكلية
        ]);

        $imagePath = $listing->image_path;

        if ($request->hasFile('image')) {
            if ($listing->image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($listing->image_path);
            }
            $imagePath = $request->file('image')->store('listings', 'public');
        }

        // جلب الصنف الأول المحدث لحفظه بالجدول الرئيسي لضمان التوافقية المستقرة [8]
        $firstItem = $request->items[0];

        // 1. تحديث بيانات الشحنة الكبرى الأم (Listing) [8]
        $listing->update([
            'title' => $firstItem['title'],
            'description' => $firstItem['description'] ?? 'شحنة فائض طعام متعددة.',
            'quantity' => $firstItem['quantity'],
            'category' => $firstItem['category'],
            'expiry_time' => $firstItem['expiry_time'],
            'address' => $request->address,
            'latitude' => $request->latitude ?? $listing->latitude,
            'longitude' => $request->longitude ?? $listing->longitude,
            'image_path' => $imagePath,
        ]);

        // 2. [حوكمة وتعديل السلة]: حذف الأصناف الفرعية القديمة بالكامل، وإعادة بناء وحفظ الأصناف الجديدة المحدثة [8]
        $listing->items()->delete();
        foreach ($request->items as $item) {
            $listing->items()->create([
                'title' => $item['title'],
                'quantity' => $item['quantity'],
                'category' => $item['category'],
                'expiry_time' => $item['expiry_time'],
                'description' => $item['description'] ?? null,
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'تم تحديث شحنة الفائض وكامل الأصناف بداخل السلة بنجاح.');
    }

    /**
     * حذف الإعلان نهائياً من النظام
     */
    public function destroy(Listing $listing)
    {
        $user = Auth::user();

        if ($listing->user_id !== $user->id) {
            return redirect()->route('dashboard')->with('error', 'غير مصرح لك بحذف هذا الإعلان.');
        }

        if (in_array($listing->status, ['picked_up', 'completed'])) {
            return redirect()->route('dashboard')->with('error', 'لا يمكن حذف إعلان قيد التوصيل أو مكتمل بالفعل.');
        }

        $listing->delete();

        return redirect()->route('dashboard')->with('success', 'تم حذف الإعلان نهائياً من المنصة.');
    }

    /**
     * إلغاء الحجز (التراجع عن المنح أو تراجع الجمعية عن الاستلام)
     */
    public function cancel(Listing $listing)
    {
        $user = Auth::user();

        if ($user->id !== $listing->user_id && $user->id !== $listing->receiver_id) {
            return redirect()->route('dashboard')->with('error', 'غير مصرح لك بإلغاء حجز هذا الطلب.');
        }

        if (!in_array($listing->status, ['reserved', 'picked_up'])) {
            return redirect()->route('dashboard')->with('error', 'لا يمكن إلغاء الحجز في الحالة الحالية للطلب.');
        }

        $driver = $listing->driver;
        if ($driver) {
            $driver->notify(new \App\Notifications\DeliveryCancelledNotification($listing));
        }

        $listing->update([
            'status' => 'available',
            'receiver_id' => null,
            'driver_id' => null,
            'verification_code' => null,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'تم إلغاء الحجز بنجاح وإعادة إتاحة الوجبة لجمعيات أخرى في المنصة.');
    }

    /**
     * استقبال الصورة المرفوعة وتحليلها بالذكاء الاصطناعي البصري مع التحقق الأمني من المحتوى
     */
    public function analyzeImage(Request $request, AIService $aiService)
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:3072'],
        ]);

        $result = $aiService->analyzeFoodImage($request->file('image'));

        if (isset($result['is_food']) && $result['is_food'] === false) {
            return response()->json([
                'success' => false,
                'message' => $result['error_message'] ?? 'الصورة المرفوعة لا تحتوي على أطعمة صالحة للتبرع في المنصة.'
            ], 422);
        }

        return response()->json(array_merge(['success' => true], $result));
    }

    /**
     * جلب بيانات التبرعات الحقيقية وتوليد تقرير الأثر البيئي بالذكاء الاصطناعي
     */
    public function generateGreenReport(AIService $aiService)
    {
        $user = Auth::user();

        if ($user->role !== 'donor') {
            return response()->json(['error' => 'عملية غير مصرح بها.'], 403);
        }

        $organizationName = $user->profile?->organization_name ?? $user->name;
        $completedCount = $user->listings()->where('status', 'completed')->count();

        $reportData = $aiService->generateSustainabilityReport($organizationName, $completedCount);

        return response()->json($reportData);
    }
}