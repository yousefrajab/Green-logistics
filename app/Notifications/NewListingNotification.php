<?php

namespace App\Notifications;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewListingNotification extends Notification
{
    use Queueable;

    protected Listing $listing;

    /**
     * استقبال الإعلان المنشور لحساب بياناته
     */
    public function __construct(Listing $listing)
    {
        $this->listing = $listing;
    }

    /**
     * نحدد هنا أننا سنقوم بحفظ الإشعار داخل قاعدة البيانات
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * هيكلة البيانات والرسالة التي ستظهر للجمعية الخيرية
     */
    public function toArray($notifiable): array
    {
        return [
            'listing_id' => $this->listing->id,
            'title' => $this->listing->title,
            'quantity' => $this->listing->quantity,
            'donor_name' => $this->listing->user?->profile?->organization_name ?? $this->listing->user?->name,
            'message' => 'تمت إضافة وجبة جديدة بالقرب منك: (' . $this->listing->title . ') بقسم ' . $this->listing->quantity,
        ];
    }
}