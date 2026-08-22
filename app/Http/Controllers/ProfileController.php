<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * عرض صفحة تعديل الملف الشخصي
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * تحديث بيانات الحساب والملف الشخصي بالكامل
     */
    /**
     * تحديث بيانات الحساب والملف الشخصي بالكامل
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // 1. التحقق من المدخلات بدقة بالغة
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['required', 'string', 'max:20'],
            'organization_name' => ['required_if:role,donor,receiver', 'nullable', 'string', 'max:255'],
            'address' => ['required_if:role,donor,receiver', 'nullable', 'string', 'max:500'],
            'license_number' => ['required_if:role,receiver', 'nullable', 'string', 'max:100'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // الصورة اختيارية وبحجم أقصى 2 ميجابايت
        ]);

        // =========================================================
        // [كود الأمان الذاتي - Self-Healing Database State]
        // إذا كان الحساب الحالي لا يملك سطراً في جدول profiles (مثل الأدمن)، ننشئه له فوراً تلقائياً
        // =========================================================
        if (!$user->profile) {
            $user->profile()->create([
                'phone' => $request->phone,
                'organization_name' => $request->organization_name,
                'address' => $request->address,
                'license_number' => $request->license_number,
            ]);
            $user->load('profile'); // إعادة تحميل العلاقة في النظام
        }

        // 2. تحديث بيانات المستخدم الأساسية
        $user->fill([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // 3. تجهيز بيانات الملف الشخصي الإضافية للشركاء
        $profileData = [
            'phone' => $request->phone,
            'organization_name' => $request->organization_name,
            'address' => $request->address,
            'license_number' => $request->license_number,
        ];

        // 4. معالجة رفع الصورة الشخصية (الأفاتار) بآمان
        if ($request->hasFile('avatar')) {
            // استخدام المعامل الآمن ?-> لضمان عدم حدوث انهيار حتى لو كانت القيمة null [6]
            if ($user->profile?->avatar) {
                Storage::disk('public')->delete($user->profile->avatar);
            }

            // رفع الصورة الجديدة وتخزينها في مجلد avatars العام
            $path = $request->file('avatar')->store('avatars', 'public');
            $profileData['avatar'] = $path;
        }

        // تحديث جدول الـ Profile المرتبط بالمستخدم
        $user->profile()->update($profileData);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * تحديث كلمة المرور بشكل مستقل للآمان
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return Redirect::route('profile.edit')->with('status', 'password-updated');
    }

    /**
     * حذف حساب المستخدم نهائياً
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        // حذف ملف الأفاتار من السيرفر قبل حذف الحساب
        if ($user->profile->avatar) {
            Storage::disk('public')->delete($user->profile->avatar);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}