<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('لوحة التحكم - ') }} 
                @if(Auth::user()->role === 'donor') جهة متبرعة
                @elseif(Auth::user()->role === 'receiver') جمعية خيرية
                @elseif(Auth::user()->role === 'driver') مندوب توصيل
                @endif
            </h2>
            <span class="px-3 py-1 text-sm rounded-full 
                @if(Auth::user()->status === 'active') bg-green-100 text-green-800
                @else bg-yellow-100 text-yellow-800 @endif">
                {{ Auth::user()->status === 'active' ? 'حساب نشط وموثق' : 'بانتظار توثيق الإدارة' }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- إذا كان الحساب غير نشط (بانتظار تفعيل الإدارة)، نعرض رسالة تنبيه ناعمة وأنيقة -->
            @if(Auth::user()->status === 'pending_verification')
                <div class="bg-yellow-50 border-r-4 border-yellow-400 p-4 mb-6 rounded-md shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <!-- أيقونة تنبيه -->
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="mr-3">
                            <p class="text-sm text-yellow-700">
                                حسابك بانتظار مراجعة الإدارة للوثائق الرسمية. يمكنك تصفح لوحة التحكم حالياً، ولكن لن تتمكن من إضافة إعلانات جديدة أو حجز وجبات حتى يتم تفعيل حسابك.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- استدعاء لوحة التحكم الخاصة بكل دور بشكل ديناميكي ومستقل -->
            @if(Auth::user()->role === 'donor')
                @include('dashboards.donor')
            @elseif(Auth::user()->role === 'receiver')
                @include('dashboards.receiver')
            @elseif(Auth::user()->role === 'driver')
                @include('dashboards.driver')
            @endif

        </div>
    </div>
</x-app-layout>