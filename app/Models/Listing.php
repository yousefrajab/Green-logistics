<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Listing extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'quantity',
        'category',
        'expiry_time',
        'status',
        'latitude',
        'longitude',
        'address',
        'receiver_id', // أضف هذا السطر هنا للسماح بحفظ الجمعية المستلمة
        'driver_id',
    ];

    // تحويل وقت انتهاء الصلاحية إلى كائن Carbon للتعامل معه بسهولة كأوقات وتواريخ
    protected $casts = [
        'expiry_time' => 'datetime',
    ];

    // العلاقة: كل إعلان ينتمي لمتبرع واحد (User)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // العلاقة: الإعلان قد ينتمي لجمعية مستلمة واحدة
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // العلاقة: الإعلان قد ينتمي لمندوب موصل واحد
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
