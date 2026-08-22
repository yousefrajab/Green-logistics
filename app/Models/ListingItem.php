<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingItem extends Model
{
    protected $fillable = [
        'listing_id',
        'title',
        'quantity',
        'category',
        'expiry_time',
        'description',
        'image_path',
    ];

    protected $casts = [
        'expiry_time' => 'datetime',
    ];

    // العلاقة: كل صنف ينتمي لشحنة تبرع أم واحدة
    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class, 'listing_id');
    }
}