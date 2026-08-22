<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // [تحديث أمني]: استيراد سمة تفعيل رموز الـ API من Sanctum [1]

class User extends Authenticatable
{
    // [تحديث أمني]: تفعيل السمة داخل الكلاس لتوليد وحفظ مفاتيح الربط الذكية [1]
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * الحقول المسموح بتعبئتها في قاعدة البيانات
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    /**
     * الحقول المخفية لأسباب أمنية
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * تحويل صياغة البيانات تلقائياً
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * علاقة المستخدم بملفه الشخصي (One-to-One)
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * علاقة المتبرع بالإعلانات والشحنات (One-to-Many)
     */
    public function listings()
    {
        return $this->hasMany(Listing::class, 'user_id');
    }
}