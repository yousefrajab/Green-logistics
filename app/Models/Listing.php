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
        'verification_code',
        'image_path',
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

    /**
     * حساب نسبة الملاءمة اللوجستية الذكية للجمعية الحالية حاسوبياً [2]
     */
    public function calculateMatchScore($charityUser)
    {
        // 1. حساب المسافة الجغرافية الحقيقية بين المتبرع والجمعية الحالية بالـ GPS (معادلة هافرسين) [2]
        $lat1 = $this->latitude;
        $lng1 = $this->longitude;
        $lat2 = $charityUser->profile->latitude ?? 31.5000;
        $lng2 = $charityUser->profile->longitude ?? 34.4667;

        $earthRadius = 6371; // نصف قطر الأرض بالكيلومترات
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c; // المسافة الفعلية بالكيلومترات بين الفندق والجمعية [2]

        // 2. حساب عامل المسافة (كلما كانت الجمعية أقرب، يرتفع التقييم اللوجستي) [2]
        $distanceScore = 100;
        if ($distance > 1) {
            $distanceScore = max(35, 100 - ($distance * 6)); // تقليل التقييم بمعدل 6% لكل كيلومتر مسافة تبعدها الجمعية
        }

        // 3. حساب عامل سرعة التلف والصلاحية الزمنية
        $hoursRemaining = now()->diffInHours($this->expiry_time, false);
        $perishabilityScore = 100;

        if ($this->category === 'cooked') {
            // الطعام المطبوخ حرج جداً ويحتاج استهلاكاً فورياً وسريعاً
            if ($hoursRemaining < 3) {
                // إذا كان متبقٍ أقل من 3 ساعات، تنحصر الملاءمة العالية فقط في محيط 5 كم للسرعة
                $perishabilityScore = $distance < 5 ? 100 : 35;
            } else {
                $perishabilityScore = 90;
            }
        } elseif ($this->category === 'fresh') {
            $perishabilityScore = 80; // الأطعمة الطازجة كالخضار متوسطة الاستقرار
        } else {
            $perishabilityScore = 65; // الأطعمة الجافة مستقرة جداً ولا تقيدها المسافة كثيراً
        }

        // 4. دمج العوامل اللوجستية بنسب وزنية مدروسة (60% مسافة جغرافية، 40% صلاحية زمنية)
        $finalScore = round(($distanceScore * 0.6) + ($perishabilityScore * 0.4));

        // حصر التقييم بين 40% و 99% لمظهر جمالي وواقعي في الواجهة
        return min(99, max(40, $finalScore));
    }


    // العلاقة: شحنة التبرع الواحدة تحتوي على عدة أصناف طعام فرعية (One-to-Many)
    public function items()
    {
        return $this->hasMany(ListingItem::class, 'listing_id');
    }
}
