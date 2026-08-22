<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * الموافقة السريعة وتفعيل الحساب
     */
    public function approve(User $user)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'عملية غير مصرح بها.');
        }

        $user->update(['status' => 'active']);
        $user->profile()->update(['verified_at' => now()]);

        return redirect()->route('dashboard')
            ->with('success', 'تم تفعيل حساب (' . $user->name . ') بنجاح.');
    }

    /**
     * رفض أو تعليق حساب
     */
    public function reject(User $user)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'عملية غير مصرح بها.');
        }

        $user->update(['status' => 'suspended']);

        return redirect()->route('dashboard')
            ->with('warning', 'تم تعليق حساب (' . $user->name . ') مؤقتاً.');
    }

    /**
     * التغيير الفوري والحركي لحالة أي مستخدم بالمنصة (مغير الحالة الذكي)
     */
    public function updateStatus(Request $request, User $user)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'عملية غير مصرح بها.');
        }

        $request->validate([
            'status' => ['required', 'string', 'in:active,pending_verification,suspended'],
        ]);

        $user->update([
            'status' => $request->status,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'تم تحديث حالة حساب المستخدم (' . $user->name . ') إلى الحالة الجديدة بنجاح.');
    }
}