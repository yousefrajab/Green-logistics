<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold text-emerald-600">لوحة التحكم</p>
                <h2 class="mt-1 text-2xl font-extrabold text-slate-900">
                    @if(Auth::user()->role === 'donor') مرحباً، جهة متبرعة
                    @elseif(Auth::user()->role === 'receiver') مرحباً، جمعية خيرية
                    @elseif(Auth::user()->role === 'driver') مرحباً، مندوب توصيل
                    @endif
                </h2>
            </div>

            <span class="inline-flex w-fit items-center gap-1.5 rounded-full px-3 py-1.5 text-sm font-semibold
                @if(Auth::user()->status === 'active') bg-emerald-50 text-emerald-700
                @else bg-amber-50 text-amber-700 @endif">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if(Auth::user()->status === 'active')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    @endif
                </svg>
                {{ Auth::user()->status === 'active' ? 'حساب نشط وموثق' : 'بانتظار توثيق الإدارة' }}
            </span>
        </div>
    </x-slot>

    <!-- إذا كان الحساب غير نشط (بانتظار تفعيل الإدارة)، نعرض رسالة تنبيه ناعمة وأنيقة -->
    @if(Auth::user()->status === 'pending_verification')
        <div class="mb-6 flex items-start gap-3 rounded-2xl border border-amber-100 bg-amber-50 p-4">
            <svg class="h-5 w-5 shrink-0 text-amber-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm leading-6 text-amber-800">
                حسابك بانتظار مراجعة الإدارة للوثائق الرسمية. يمكنك تصفح لوحة التحكم حالياً، ولكن لن تتمكن من إضافة إعلانات جديدة أو حجز وجبات حتى يتم تفعيل حسابك.
            </p>
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
</x-app-layout>
