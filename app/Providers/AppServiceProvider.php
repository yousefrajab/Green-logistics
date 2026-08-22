<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Listing;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // View Composer لحقن أرقام الإحصائيات الحقيقية تلقائياً في صفحة الدخول والتسجيل
        View::composer('layouts.guest', function ($view) {
            
            // 1. حساب عدد الوجبات التي تم إنقاذها وتوصيلها بنجاح (حقيقي)
            $completedCount = Listing::where('status', 'completed')->sum('quantity');

            
            // 2. حساب عدد مناديب التوصيل النشطين في المنصة (حقيقي)
            $activeDrivers = User::where('role', 'driver')->where('status', 'active')->count();
            
            // 3. حساب عدد الجمعيات الخيرية الشريكة والنشطة في المنصة (حقيقي)
            $partnerCharities = User::where('role', 'receiver')->where('status', 'active')->count();

            // تمرير الأرقام تلقائياً لقالب الضيوف
            $view->with(compact('completedCount', 'activeDrivers', 'partnerCharities'));
        });
    }
}