<?php

namespace App\Notifications;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DeliveryCancelledNotification extends Notification
{
    use Queueable;

    protected Listing $listing;

    public function __construct(Listing $listing)
    {
        $this->listing = $listing;
    }

    public function via($notifiable): array
    {
        return ['database']; // حفظه بجدول الإشعارات لعرضه في جرس السائق
    }

    public function toArray($notifiable): array
    {
        return [
            'listing_id' => $this->listing->id,
            'message' => '⚠️ تنبيه طارئ: تم إلغاء حجز وجبة (' . $this->listing->title . ') التي كنت مكلفاً بتوصيلها.',
        ];
    }
}