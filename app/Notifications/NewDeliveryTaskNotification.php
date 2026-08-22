<?php

namespace App\Notifications;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewDeliveryTaskNotification extends Notification
{
    use Queueable;

    protected Listing $listing;

    public function __construct(Listing $listing)
    {
        $this->listing = $listing;
    }

    public function via($notifiable): array
    {
        return ['database']; // حفظ التنبيه في قاعدة البيانات ليظهر في جرس المندوب
    }

    public function toArray($notifiable): array
    {
        return [
            'listing_id' => $this->listing->id,
            'title' => $this->listing->title,
            'quantity' => $this->listing->quantity,
            'donor_name' => $this->listing->user?->profile?->organization_name ?? $this->listing->user?->name,
            'receiver_name' => $this->listing->receiver?->profile?->organization_name ?? $this->listing->receiver?->name,
            'message' => 'مهمة توصيل جديدة! تم حجز (' . $this->listing->title . ') من قِبل جمعية، اضغط لقبول التوصيل.',
        ];
    }
}