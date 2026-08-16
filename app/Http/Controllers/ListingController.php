<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AIService;

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
    public function store(Request $request, AIService $aiService) // قمنا بحقن الخدمة هنا
    {
        $user = Auth::user();

        if ($user->role !== 'donor' || $user->status !== 'active') {
            return redirect()->route('dashboard')->with('error', 'عملية غير مصرح بها.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'quantity' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'in:cooked,dry,fresh'],
            'expiry_time' => ['required', 'date', 'after:now'],
            'address' => ['required', 'string', 'max:500'],
        ]);

        $description = $request->description;
        $allergens = null;

        // إذا لم يكتب المتبرع وصفاً للوجبة، نطلق كود الذكاء الاصطناعي تلقائياً لصياغة الوصف واقتراح الحساسية
        if (empty($description)) {
            $aiResult = $aiService->generateDescriptionAndAllergens($request->title);

            $description = $aiResult['description'];

            // سنقوم بدمج تنبيه الحساسية في نهاية الوصف ليظهر بوضوح للجمعية المستلمة
            if (!empty($aiResult['allergens'])) {
                $description .= "\n\n⚠️ " . $aiResult['allergens'];
            }
        }

        $user->listings()->create([
            'title' => $request->title,
            'description' => $description, // الوصف الذكي المولد
            'quantity' => $request->quantity,
            'category' => $request->category,
            'expiry_time' => $request->expiry_time,
            'address' => $request->address,
            'latitude' => $user->profile->latitude ?? 31.5000,
            'longitude' => $user->profile->longitude ?? 34.4667,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'تم نشر إعلان الفائض بنجاح! تم استخدام الذكاء الاصطناعي لصياغة الوصف والتحقق من مسببات الحساسية.');
    }

    /**
     * تمكين الجمعية من حجز الوجبة الفائضة
     */
    public function reserve(Listing $listing)
    {
        $user = Auth::user();

        // التحقق من أن الحساب بصلاحية جمعية (receiver) وأنه نشط
        if ($user->role !== 'receiver' || $user->status !== 'active') {
            return redirect()->back()->with('error', 'عذراً، يجب تفعيل حسابك كجمعية لتتمكن من حجز الوجبات.');
        }

        // التحقق من أن السلعة لا زالت متاحة ولم يحجزها أحد آخر بعد
        if ($listing->status !== 'available') {
            return redirect()->back()->with('error', 'عذراً، هذه الوجبة تم حجزها بالفعل من قِبل جهة أخرى.');
        }

        // تحديث حالة الوجبة وتسجيل معرف الجمعية المستلمة
        $listing->update([
            'status' => 'reserved',
            'receiver_id' => $user->id,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'تم حجز الوجبة بنجاح! يرجى التنسيق للاستلام في أسرع وقت.');
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
    public function completeDelivery(Listing $listing)
    {
        $user = Auth::user();

        if ($listing->driver_id !== $user->id) {
            return redirect()->back()->with('error', 'عملية غير مصرح بها.');
        }

        $listing->update([
            'status' => 'completed',
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'رائع! تم تسليم الشحنة بنجاح واكتمال العملية الخيرية.');
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
    public function update(Request $request, Listing $listing)
    {
        $user = Auth::user();

        if ($listing->user_id !== $user->id || $listing->status === 'completed') {
            return redirect()->route('dashboard')->with('error', 'عملية غير مصرح بها.');
        }

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'quantity' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'in:cooked,dry,fresh'],
            'expiry_time' => ['required', 'date', 'after:now'],
            'address' => ['required', 'string', 'max:500'],
        ]);

        $listing->update($request->only([
            'title',
            'description',
            'quantity',
            'category',
            'expiry_time',
            'address'
        ]));

        return redirect()->route('dashboard')->with('success', 'تم تحديث بيانات إعلان الفائض بنجاح.');
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
    public function cancel(Listing $listing)
    {
        $user = Auth::user();

        // المسموح لهم بالإلغاء: صاحب الإعلان (المتبرع) أو الجمعية التي حجزته فقط
        if ($user->id !== $listing->user_id && $user->id !== $listing->receiver_id) {
            return redirect()->route('dashboard')->with('error', 'غير مصرح لك بإلغاء حجز هذا الطلب.');
        }

        // يمكن الإلغاء فقط إذا كان الطلب "محجوزاً" أو "قيد التوصيل" ولم يكتمل بعد
        if (!in_array($listing->status, ['reserved', 'picked_up'])) {
            return redirect()->route('dashboard')->with('error', 'لا يمكن إلغاء الحجز في الحالة الحالية للطلب.');
        }

        // إرجاع حالة الوجبة لتصبح "متاحة" مجدداً للجميع، وتصفير المندوب والجمعية
        $listing->update([
            'status' => 'available',
            'receiver_id' => null,
            'driver_id' => null,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'تم إلغاء الحجز بنجاح وإعادة إتاحة الوجبة لجمعيات أخرى في المنصة.');
    }
}
