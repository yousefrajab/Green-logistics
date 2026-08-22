<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            <div class="space-y-1">
                <h2 class="font-extrabold text-2xl text-slate-900 leading-tight">لوحة التحكم والمتابعة</h2>
                <p class="text-xs text-slate-400 font-medium">مرحباً بك مجدداً في نظام سلسلة حفظ النعمة اللوجستية</p>
            </div>
            
            <!-- أزرار اختيار سريعة للغة وتحديث البيانات -->
            <div class="flex items-center space-x-2 space-x-reverse">
                <button class="p-2 text-slate-400 hover:text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.2" />
                    </svg>
                </button>
            </div>
        </div>
    </x-slot>

    <!-- مساحة العمل الإبداعية للمشروع -->
    <div class="space-y-8">
        
        <!-- قسم التنبيهات الذكية اللطيفة -->
        @if(Auth::user()->status === 'pending_verification')
            <div class="bg-gradient-to-r from-amber-500/10 to-orange-500/10 border border-amber-500/20 p-5 rounded-2xl shadow-sm flex items-start space-x-4 space-x-reverse backdrop-blur-md">
                <div class="p-2.5 bg-amber-500/20 text-amber-600 rounded-xl">
                    <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="space-y-1">
                    <h4 class="text-sm font-bold text-amber-800">الحساب قيد المراجعة الأمنية والتوثيق</h4>
                    <p class="text-xs text-amber-700/80 leading-relaxed max-w-2xl">
                        يرجى الانتظار لحين قيام مشرفي المنصة بمراجعة وتأكيد الوثائق الرسمية والتراخيص لضمان أمان سلسلة الغذاء. يمكنك تصفح واجهات الاستلام والتسليم حالياً، ولكن تفعيل الصلاحيات الكاملة سيتم فور الموافقة.
                    </p>
                </div>
            </div>
        @endif

        <!-- =========================================================
             قسم الرسوم البيانية الإبداعي والتفاعلي (مطابق لمفهوم الصورة 11)
        ========================================================== -->
        <!-- =========================================================
             قسم الرسوم البيانية الديناميكي المرتبط بقاعدة البيانات 100%
        ========================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- الرسوم البيانية الدائرية لمؤشرات الأداء الحقيقية -->
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between space-y-6">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">معدل تلبية الطلبات</h3>
                    <p class="text-xs text-slate-400 mt-1">نسبة نجاح التوصيل الفعلي من إجمالي الحجوزات</p>
                </div>
                
                <!-- رسم تفاعلي دائري يتغير انحنائه برمجياً -->
                <div class="relative flex items-center justify-center">
                    <svg class="w-36 h-36 transform -rotate-90">
                        <circle cx="72" cy="72" r="60" stroke="#f1f5f9" stroke-width="12" fill="transparent" />
                        <!-- نمرر هنا الـ dashoffset المحسوب من قاعدة البيانات -->
                        <circle cx="72" cy="72" r="60" stroke="#10b981" stroke-width="12" fill="transparent" 
                                stroke-dasharray="376.8" stroke-dashoffset="{{ $dashoffset }}" stroke-linecap="round" class="transition-all duration-1000 ease-out" />
                    </svg>
                    <div class="absolute text-center space-y-0.5">
                        <!-- نمرر النسبة المئوية الحقيقية -->
                        <span class="text-2xl font-extrabold text-slate-800">{{ $fulfillmentRate }}%</span>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">كفاءة حقيقية</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 text-center border-t border-slate-100 pt-4 text-xs font-semibold text-slate-500">
                    <div>
                        <span class="text-emerald-500 font-bold">● مكتمل</span>
                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $completedCount }} عملية</p>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold">● جاري</span>
                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $activeCount }} عمليات</p>
                    </div>
                </div>
            </div>

            <!-- المخطط البياني بالأعمدة المرتبط بأيام الأسبوع الحقيقية في قاعدة البيانات -->
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between space-y-4 lg:col-span-2">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">حجم الأغذية التي تم إنقاذها أسبوعياً</h3>
                        <p class="text-xs text-slate-400 mt-1">تتبع التبرعات خلال الأيام السبعة الأخيرة من قاعدة البيانات</p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">إجمالي العمليات: {{ $completedCount }}</span>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">إجمالي الوجبات: {{ $completeCount }}</span>

                </div>

                <!-- تمثيل الأعمدة البيانية التفاعلية الديناميكية -->
                <div class="flex items-end justify-between h-44 pt-6 pb-2 px-2 border-b border-slate-100">
                    @foreach($weeklyStats as $day => $count)
                        @php
                            // نحدد نسبة ارتفاع العمود برمجياً بناءً على تكرار تبرعات ذلك اليوم
                            $height = $weeklyHeights[$day] ?? 5;
                            // نضع حداً أدنى للارتفاع (مثلاً 10%) لكي يظهر العمود بشكل جمالي حتى لو كان الصفر
                            if ($height < 10) $height = 10;
                        @endphp
                        <div class="flex flex-col items-center space-y-2 group w-full">
                            <!-- التلميح يظهر عند تمرير الفأرة فوق العمود ليعرض عدد العمليات الفعلي في ذلك اليوم -->
                            <div class="relative w-8 bg-slate-100 rounded-t-lg h-36 flex items-end overflow-hidden cursor-pointer">
                                <div class="w-full bg-gradient-to-t from-emerald-500 to-teal-400 rounded-t-lg group-hover:opacity-90 transition-all" style="height: {{ $height }}%"></div>
                            </div>
                            <span class="text-[10px] font-bold text-slate-400">{{ $day }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- =========================================================
             استدعاء واجهات لوحات التحكم الفرعية والعمليات اللوجستية لكل دور
        ========================================================== -->
        <div class="pt-2">
            @if(Auth::user()->role === 'donor')
                @include('dashboards.donor')
            @elseif(Auth::user()->role === 'receiver')
                @include('dashboards.receiver')
            @elseif(Auth::user()->role === 'driver')
                @include('dashboards.driver')
            @elseif(Auth::user()->role === 'admin')
                @include('dashboards.admin')
            @endif
        </div>

    </div>
</x-app-layout>