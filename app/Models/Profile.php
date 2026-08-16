<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'organization_name',
        'phone',
        'address',
        'latitude',
        'longitude',
        'license_number',
        'document_path',
        'verified_at',
    ];

    // علاقة الملف الشخصي بالمستخدم
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}