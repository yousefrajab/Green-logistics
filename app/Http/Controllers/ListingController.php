<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AIService;
use App\Notifications\NewListingNotification;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewDeliveryTaskNotification; // أضف هذا السطر في الأعلى


class ListingController extends Controller
{
    /**
     * عرض صفحة إضافة إعلان جديد
     */
    public function create()
    {
        $user = Auth::user();

        // حماية برمجية إضافية: فقط المتبرع النشط والموثق يمكنه الإضافة
        if ($user->role !== 'donor' || $user->status !== 'active') {
            return redirect()->route('dashboard')
                ->with('error', 'عذراً، يجب أن يكون حسابك نشطاً وبصلاحية متبرع لتتمكن من إضافة إعلانات فائض.');
        }

        return view('listings.create', compact('user'));
    }

    /**
     * حفظ الإعلان الجديد في قاعدة البيانات
     */
    /**
     * حفظ الإعلان الجديد بالـ GPS الفعلي المختار من الخريطة
     */
    public function store(Request $request, AIService $aiService)
    {
        $user = Auth::user();

        if ($user->role !== 'donor' || $user->status !== 'active') {
            return redirect()->route('dashboard')->with('error', 'عملية غير مصرح بها.');
        }

        // [تحديث التحقق لمصفوفة سلة الأغذية المتعددة] [8]
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

        // جلب بيانات الصنف الأول في المصفوفة لحفظها بالجدول الرئيسي لضمان التوافقية مع الجداول والخرائط القديمة [8]
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

        // 2. [كود سلة الفائض]: تكرار وحفظ جميع الأصناف الفرعية بالكامل بجدول listing_items الجديد [8]
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
     * تمكين الجمعية من حجز الوجبة الفائضة
     */
    public function reserve(Listing $listing)
    {
        $user = Auth::user();

        if ($user->role !== 'receiver' || $user->status !== 'active') {
            return redirect()->back()->with('error', 'عذراً، يجب تفعيل حسابك كجمعية لتتمكن من حجز الوجبات.');
        }

        if ($listing->status !== 'available') {
            return redirect()->back()->with('error', 'عذراً، هذه الوجبة تم حجزها بالفعل من قِبل جهة أخرى.');
        }

        // 1. تحديث حالة الوجبة وتسجيل الجمعية المستلمة
        $listing->update([
            'status' => 'reserved',
            'receiver_id' => $user->id,
            'verification_code' => rand(1000, 9999), // توليد رمز أمان عشوائي [8]
        ]);

        // 2. [إرسال إشعار فوري لجميع المناديب بتوفر شحنة جديدة للتوصيل]
        $activeDrivers = \App\Models\User::where('role', 'driver')->where('status', 'active')->get();

        // كود احتياطي للتسهيل أثناء فترة التجربة المحلية في حال لم تكن حسابات المناديب مفعلة كـ active بعد
        if ($activeDrivers->isEmpty()) {
            $activeDrivers = \App\Models\User::where('role', 'driver')->get();
        }

        if ($activeDrivers->isNotEmpty()) {
            Notification::send($activeDrivers, new NewDeliveryTaskNotification($listing));
        }

        return redirect()->route('dashboard')
            ->with('success', 'تم حجز الوجبة بنجاح! تم إشعار مناديب التوصيل القريبين للبدء بنقلها إليكم.');
    }

    /**
     * قبول المندوب لتوصيل الطلب
     */
    public function acceptDelivery(Listing $listing)
    {
        $user = Auth::user();

        if ($user->role !== 'driver' || $user->status !== 'active') {
            return redirect()->back()->with('error', 'يجب تفعيل حسابك كمندوب لتتمكن من قبول الطلبات.');
        }

        if ($listing->status !== 'reserved' || !is_null($listing->driver_id)) {
            return redirect()->back()->with('error', 'عذراً، هذا الطلب تم قبوله بالفعل من قِبل مندوب آخر.');
        }

        $listing->update([
            'driver_id' => $user->id,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'تم قبول طلب التوصيل بنجاح! يرجى التوجه لنقطة الاستلام.');
    }

    /**
     * تأكيد استلام المندوب للشحنة من المتبرع (الفندق/المطعم)
     */
    public function pickupDelivery(Listing $listing)
    {
        $user = Auth::user();

        if ($listing->driver_id !== $user->id) {
            return redirect()->back()->with('error', 'عملية غير مصرح بها.');
        }

        $listing->update([
            'status' => 'picked_up',
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'تم تأكيد استلام الشحنة من المتبرع. يرجى التوصيل بأمان.');
    }

    /**
     * تأكيد تسليم المندوب للشحنة للجمعية واكتمال الطلب
     */
    /**
     * تأكيد تسليم الشحنة للجمعية بعد مطابقة كود الأمان بنجاح واكتمال الطلب
     */
    public function completeDelivery(Request $request, Listing $listing)
    {
        $user = Auth::user();

        if ($listing->driver_id !== $user->id) {
            return redirect()->back()->with('error', 'عملية غير مصرح بها.');
        }

        // التحقق من إدخال كود التوثيق المستلم من الجمعية [8]
        $request->validate([
            'verification_code' => ['required', 'integer'],
        ]);

        // مطابقة الكود المدخل مع الكود المخزن في قاعدة البيانات [8]
        if ((int)$request->verification_code !== $listing->verification_code) {
            return redirect()->back()->with('error', 'عذراً، رمز تأكيد التسليم غير صحيح! يرجى مراجعة الجمعية المستلمة للحصول على الرمز الصحيح.');
        }

        // تحديث حالة المعاملة لاكتمالها بنجاح بعد توثيق الأمان
        $listing->update([
            'status' => 'completed',
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'رائع! تم تسليم الشحنة وتوثيق المعاملة بنجاح بمطابقة كود الأمان.');
    }

    /**
     * عرض صفحة تعديل الإعلان
     */
    public function edit(Listing $listing)
    {
        $user = Auth::user();

        // التحقق من أن المستخدم الحالي هو صاحب الإعلان نفسه، وأن الإعلان لم يتم تسليمه بعد
        if ($listing->user_id !== $user->id || $listing->status === 'completed') {
            return redirect()->route('dashboard')->with('error', 'غير مصرح لك بتعديل هذا الإعلان في حالته الحالية.');
        }

        return view('listings.edit', compact('listing', 'user'));
    }

    /**
     * تحديث بيانات الإعلان في قاعدة البيانات
     */
    // public function update(Request $request, Listing $listing)
    // {
    //     $user = Auth::user();

    //     if ($listing->user_id !== $user->id || $listing->status === 'completed') {
    //         return redirect()->route('dashboard')->with('error', 'عملية غير مصرح بها.');
    //     }

    //     $request->validate([
    //         'title' => ['required', 'string', 'max:255'],
    //         'description' => ['nullable', 'string', 'max:1000'],
    //         'quantity' => ['required', 'string', 'max:100'],
    //         'category' => ['required', 'string', 'in:cooked,dry,fresh'],
    //         'expiry_time' => ['required', 'date', 'after:now'],
    //         'address' => ['required', 'string', 'max:500'],
    //     ]);

    //     $listing->update($request->only([
    //         'title',
    //         'description',
    //         'quantity',
    //         'category',
    //         'expiry_time',
    //         'address'
    //     ]));

    //     return redirect()->route('dashboard')->with('success', 'تم تحديث بيانات إعلان الفائض بنجاح.');
    // }

    /**
     * تحديث بيانات الإعلان مع حفظ إحداثيات التعديل وصورة الطعام الجديدة من الخريطة
     */
    public function update(Request $request, Listing $listing)
    {
        $user = Auth::user();

        if ($listing->user_id !== $user->id || $listing->status === 'completed') {
            return redirect()->route('dashboard')->with('error', 'عملية غير مصرح بها.');
        }

        // التحقق من كافة المدخلات المعدلة بما فيها الصورة الجديدة الاختيارية [8]
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'quantity' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'in:cooked,dry,fresh'],
            'expiry_time' => ['required', 'date', 'after:now'],
            'address' => ['required', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'image' => ['nullable', 'image', 'max:3072'], // صورة الوجبة بحد أقصى 3 ميجا
        ]);

        $imagePath = $listing->image_path;

        // معالجة تعديل وحفظ الصورة الجديدة وحذف القديمة من السيرفر [8]
        if ($request->hasFile('image')) {
            if ($listing->image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($listing->image_path);
            }
            $imagePath = $request->file('image')->store('listings', 'public');
        }

        // تحديث الإعلان بكافة بياناته الملاحية والوصفية والرمزية الجديدة [8]
        $listing->update([
            'title' => $request->title,
            'description' => $request->description,
            'quantity' => $request->quantity,
            'category' => $request->category,
            'expiry_time' => $request->expiry_time,
            'address' => $request->address,
            'latitude' => $request->latitude ?? $listing->latitude,
            'longitude' => $request->longitude ?? $listing->longitude,
            'image_path' => $imagePath, // حفظ مسار الصورة المعدل!
        ]);

        return redirect()->route('dashboard')->with('success', 'تم تحديث بيانات إعلان الفائض وإحداثياته وصورته بنجاح.');
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

        // لا يمكن حذف الإعلان إذا كان جاري توصيله أو تم تسليمه (لحفظ الحقوق وتتبع المندوبين)
        if (in_array($listing->status, ['picked_up', 'completed'])) {
            return redirect()->route('dashboard')->with('error', 'لا يمكن حذف إعلان قيد التوصيل أو مكتمل بالفعل.');
        }

        $listing->delete();

        return redirect()->route('dashboard')->with('success', 'تم حذف الإعلان نهائياً من المنصة.');
    }

    /**
     * إلغاء الحجز (التراجع عن المنح أو تراجع الجمعية عن الاستلام)
     */
    /**
     * إلغاء الحجز (التراجع عن المنح أو تراجع الجمعية عن الاستلام) مع إخطار المندوب
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

        // [سد الثغرة اللوجستية] [8]
        // جلب المندوب الحالي المكلف بالتوصيل (إن وجد) لإخطاره بالإلغاء فوراً قبل مسحه من قاعدة البيانات
        $driver = $listing->driver;
        if ($driver) {
            $driver->notify(new \App\Notifications\DeliveryCancelledNotification($listing));
        }

        // إرجاع حالة الوجبة لتصبح "متاحة" مجدداً للجميع، وتصفير المندوب والجمعية
        $listing->update([
            'status' => 'available',
            'receiver_id' => null,
            'driver_id' => null,
            'verification_code' => null, // تصفير كود التوثيق القديم
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'تم إلغاء الحجز بنجاح وإعادة إتاحة الوجبة لجمعيات أخرى في المنصة.');
    }
    /**
     * توليد وعرض إيصال التبرع والتسليم الموثق للجهات
     */
    public function receipt(Listing $listing)
    {
        $user = Auth::user();

        // حماية برمجية: فقط المتبرع أو الجمعية المستلمة للوجبة المكتملة يمكنهما إصدار وعرض الإيصال
        if ($user->id !== $listing->user_id && $user->id !== $listing->receiver_id) {
            abort(403, 'غير مصرح لك بعرض هذا الإيصال.');
        }

        // لا يمكن إصدار إيصال إلا للعمليات المكتملة بنجاح
        if ($listing->status !== 'completed') {
            return redirect()->route('dashboard')->with('error', 'لا يمكن إصدار إيصال تسليم لوجبة لم تكتمل دورتها اللوجستية بعد.');
        }

        // تحميل علاقات المستخدمين المطور بدقة
        $listing->load(['user.profile', 'receiver.profile', 'driver.profile']);

        return view('listings.receipt', compact('listing'));
    }

    /**
     * استقبال الصورة المرفوعة وتحليلها بالذكاء الاصطناعي البصري وإرجاع البيانات بالفورم [4]
     */
    public function analyzeImage(Request $request, AIService $aiService)
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:3072'], // بحد أقصى 3 ميجا
        ]);

        // استدعاء خدمة الذكاء الاصطناعي البصري المحدثة للتحليل والتحقق من المحتوى [4]
        $result = $aiService->analyzeFoodImage($request->file('image'));

        // [الحظر الأمني الفوري لمدير النظام]: إذا أثبت الذكاء البصري أن الصورة ليست طعاماً [4, 8]
        if (isset($result['is_food']) && $result['is_food'] === false) {
            return response()->json([
                'success' => false,
                'message' => $result['error_message'] ?? 'الصورة المرفوعة لا تحتوي على أطعمة صالحة للتبرع في المنصة.'
            ], 422); // إرجاع كود الخطأ 422 للتحذير [8]
        }

        return response()->json(array_merge(['success' => true], $result));
    }

    /**
     * جلب بيانات التبرعات الحقيقية من قاعدة البيانات وتوليد تقرير الأثر البيئي بالذكاء الاصطناعي
     */
    public function generateGreenReport(AIService $aiService)
    {
        $user = Auth::user();

        // حماية برمجية: فقط الجهات المتبرعة يمكنها توليد تقارير الأثر
        if ($user->role !== 'donor') {
            return response()->json(['error' => 'عملية غير مصرح بها.'], 403);
        }

        $organizationName = $user->profile?->organization_name ?? $user->name;

        // حساب عدد وجبات التبرع المكتملة بنجاح فقط من قاعدة البيانات لتمريرها كأرقام حقيقية
        $completedCount = $user->listings()->where('status', 'completed')->count();

        // استدعاء الخدمة الذكية لتوليد تقرير الاستدامة الفاخر [4]
        $reportData = $aiService->generateSustainabilityReport($organizationName, $completedCount);

        return response()->json($reportData);
    }



    /**
     * توليد شهادة تقدير الاستدامة والمسؤولية المجتمعية الفخمة للفنادق
     */
    public function printGreenReport(AIService $aiService)
    {
        $user = Auth::user();

        // حماية برمجية: فقط الجهات المتبرعة يمكنها تصفح وطباعة هذه الشهادة
        if ($user->role !== 'donor') {
            abort(403, 'عملية غير مصرح بها.');
        }

        $organizationName = $user->profile?->organization_name ?? $user->name;

        // جلب عدد تبرعاتهم الحقيقي المكتمل بنجاح من قاعدة البيانات
        $completedCount = Listing::where('status', 'completed')->sum('quantity');


        // استدعاء الذكاء الاصطناعي لتوليد التقرير المختصر جداً لبثه في الشهادة الرسمية [4]
        $report = $aiService->generateSustainabilityReport($organizationName, $completedCount);

        return view('listings.green_report_print', compact('organizationName', 'completedCount', 'report'));
    }

    public function generateToken(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'donor') {
            abort(403, 'غير مصرح لك بتوليد مفاتيح الربط.');
        }

        $request->validate([
            'token_name' => ['required', 'string', 'max:255'],
        ]);

        // توليد رمز وصول آمن فريد وحفظه مشفراً بـ Sanctum
        $token = $user->createToken($request->token_name)->plainTextToken;

        // نمرر الرمز الصافي مرة واحدة فقط للمستودع لكي ينسخه الفندق بأمان
        return redirect()->back()->with('success_token', $token);
    }
}
