<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register'); // واجهة التسجيل الافتراضية المنسقة لـ جود
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // تم إلغاء حقل الـ status تماماً من قائمة التحقق لمنع أي تلاعب خارجي من المستخدمين [8]
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:donor,receiver,driver'],
            'phone' => ['required', 'string', 'max:20'],
            
            'organization_name' => ['required_if:role,donor,receiver', 'nullable', 'string', 'max:255'],
            'address' => ['required_if:role,donor,receiver', 'nullable', 'string', 'max:500'],
            'license_number' => ['required_if:role,receiver', 'nullable', 'string', 'max:100'],
            
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        // [القفل الأمني التلقائي في الخلفية] [8]
        // السائقين يتم تفعيلهم فوراً، بينما الفنادق (Donors) والجمعيات (Receivers) تبدأ معلقة إجبارياً بانتظار الإدارة لتوثيقهم
        $status = 'pending_verification';

        // 1. إنشاء حساب المستخدم بالحالة المعلقة
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $status, 
        ]);

        // 2. إنشاء ملف البيانات الجغرافية والتنظيمية المصاحب
        $user->profile()->create([
            'organization_name' => $request->organization_name,
            'phone' => $request->phone,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'license_number' => $request->license_number,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}