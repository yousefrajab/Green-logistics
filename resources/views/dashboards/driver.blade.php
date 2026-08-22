<div class="space-y-6" dir="rtl">

    <!-- عرض الإشعارات -->
    @if(session('success'))
        <div class="bg-emerald-50 border-r-4 border-emerald-500 p-4 rounded-xl shadow-sm">
            <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border-r-4 border-rose-500 p-4 rounded-xl shadow-sm">
            <p class="text-sm font-semibold text-rose-800">{{ session('error') }}</p>
        </div>
    @endif

    <!-- 1. الشحنات النشطة الحالية (التي قبلها المندوب وجاري العمل عليها) -->
    <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6">
        <h3 class="text-lg font-bold text-slate-900 mb-4">شحناتي النشطة الحالية</h3>
        
        @php
            $myActiveDeliveries = \App\Models\Listing::with(['user.profile', 'receiver.profile'])
                ->where('driver_id', Auth::user()->id)
                ->whereIn('status', ['reserved', 'picked_up'])
                ->get();
        @endphp

        @if($myActiveDeliveries->isEmpty())
            <p class="text-sm text-slate-400">ليس لديك أي شحنات قيد التوصيل حالياً. تصفح الطلبات المتاحة في الأسفل للبدء.</p>
        @else
            <div class="grid grid-cols-1 gap-6">
                @foreach($myActiveDeliveries as $delivery)
                    <!-- مجهز بكلاس ومعرف التوجيه الذاتي والتوهج اللحظي -->
                    <div id="listing-{{ $delivery->id }}" class="listing-card-target border border-slate-100 rounded-2xl p-6 bg-slate-50/50 flex flex-col md:flex-row justify-between items-start md:items-center scroll-mt-24 transition-all duration-300">
                        <div class="space-y-3">
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <h4 class="text-md font-bold text-slate-900">{{ $delivery->title }}</h4>
                                @if($delivery->status === 'reserved')
                                    <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-yellow-50 text-yellow-700 border border-yellow-200">تم القبول - توجه للاستلام</span>
                                @else
                                    <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-blue-50 text-blue-700 border border-blue-200">قيد التوصيل للجمعية</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-400 font-semibold">📦 الكمية: {{ $delivery->quantity }} | 📁 التصنيف: {{ $delivery->category }}</p>
                            
                            <!-- مسار التوصيل المنسق كـ تذكرة شحن مع أزرار تتبع خرائط جوجل الملاحية للـ GPS -->
                            <div class="text-xs text-slate-600 space-y-2 pt-2 border-t border-slate-200/50">
                                <div class="flex items-start">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ml-2 mt-1 shrink-0 animate-pulse"></span>
                                    <p>
                                        <strong class="text-green-700">1. من المتبرع (الاستلام):</strong> 
                                        <span class="font-bold text-slate-800">{{ $delivery->user?->profile?->organization_name ?? $delivery->user?->name }}</span> - العنوان: {{ $delivery->address }}
                                        <!-- زر تتبع الاتجاهات للفندق بخرائط جوجل -->
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ $delivery->latitude ?? 31.5000 }},{{ $delivery->longitude ?? 34.4667 }}" target="_blank" class="text-emerald-600 hover:text-emerald-800 font-bold mr-2 inline-flex items-center">
                                            🗺️ تتبع الاتجاهات
                                        </a>
                                    </p>
                                </div>
                                <div class="flex items-start">
                                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 ml-2 mt-1 shrink-0"></span>
                                    <p>
                                        <strong class="text-indigo-700">2. إلى الجمعية (التسليم):</strong> 
                                        <span class="font-bold text-slate-800">{{ $delivery->receiver?->profile?->organization_name ?? $delivery->receiver?->name }}</span> - العنوان: {{ $delivery->receiver?->profile?->address ?? 'عنوان الجمعية غير متوفر' }}
                                        <!-- زر تتبع الاتجاهات للجمعية بخرائط جوجل -->
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ $delivery->receiver?->profile?->latitude ?? 31.5000 }},{{ $delivery->receiver?->profile?->longitude ?? 34.4667 }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 font-bold mr-2 inline-flex items-center">
                                            🗺️ تتبع الاتجاهات
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- أزرار التوصيل الديناميكية وتحويل الحالات -->
                        <div class="mt-4 md:mt-0">
                            @if($delivery->status === 'reserved')
                                <!-- خطوة 1: تأكيد الاستلام من الفندق -->
                                <form method="POST" action="{{ route('listings.pickup-delivery', $delivery->id) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700">
                                        تأكيد استلام الشحنة من المتبرع
                                    </button>
                                </form>
                            @elseif($delivery->status === 'picked_up')
                                <!-- خطوة 2 المحدثة: إدخال كود التوثيق لإكمال التوصيل بأمان -->
                                <form method="POST" action="{{ route('listings.complete-delivery', $delivery->id) }}" class="flex flex-col sm:flex-row items-center gap-3">
                                    @csrf
                                    <div>
                                        <input type="text" name="verification_code" required placeholder="أدخل رمز الاستلام (4 أرقام)" class="text-xs font-bold border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl py-2.5 px-4 w-48 text-center bg-white shadow-sm">
                                    </div>
                                    <button type="submit" class="inline-flex items-center px-4 py-2.5 border border-transparent text-xs font-bold rounded-xl shadow-md shadow-emerald-100 text-white bg-green-600 hover:bg-green-700 transition-colors">
                                        تأكيد تسليم الشحنة واكتمالها
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- 2. طلبات التوصيل المتاحة بالمنصة -->
    <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6">
        <h3 class="text-lg font-bold text-slate-900 mb-4">طلبات توصيل متاحة حالياً</h3>
        
        @php
            $availableDeliveries = \App\Models\Listing::with(['user.profile', 'receiver.profile'])
                ->where('status', 'reserved')
                ->whereNull('driver_id')
                ->orderBy('updated_at', 'desc')
                ->get();
        @endphp

        @if($availableDeliveries->isEmpty())
            <div class="text-center py-8 border border-dashed border-gray-200 rounded-lg">
                <p class="text-sm text-gray-500">لا توجد طلبات توصيل معلقة حالياً في المنصة.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($availableDeliveries as $delivery)
                    <!-- مجهز بكلاس ومعرف التوجيه الذاتي والتوهج اللحظي -->
                    <div id="listing-{{ $delivery->id }}" class="listing-card-target border border-slate-100 rounded-2xl p-5 bg-slate-50/50 flex flex-col justify-between hover:shadow-md transition-all duration-300 scroll-mt-24">
                        <div>
                            <h4 class="text-md font-bold text-slate-900">{{ $delivery->title }}</h4>
                            <p class="text-xs text-slate-400 mt-1">📦 الكمية: {{ $delivery->quantity }} | 🕒 صالح حتى: {{ $delivery->expiry_time->format('Y-m-d H:i') }}</p>
                            
                            <!-- جهات الاستلام والتسليم المأمنة مع روابط خرائط جوجل للمعاينة المسبقة للمسار -->
                            <div class="mt-4 space-y-2 text-xs text-slate-600 border-t border-slate-200/60 pt-3">
                                <p>
                                    <strong>📍 نقطة الاستلام:</strong> 
                                    {{ $delivery->user?->profile?->organization_name ?? $delivery->user?->name }} ({{ $delivery->address }})
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $delivery->latitude ?? 31.5000 }},{{ $delivery->longitude ?? 34.4667 }}" target="_blank" class="text-emerald-600 hover:text-emerald-800 font-bold mr-1.5">🗺️ معاينة الاستلام</a>
                                </p>
                                <p>
                                    <strong>🏁 نقطة التسليم:</strong> 
                                    {{ $delivery->receiver?->profile?->organization_name ?? $delivery->receiver?->name }} ({{ $delivery->receiver?->profile?->address ?? 'مقر الجمعية' }})
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $delivery->receiver?->profile?->latitude ?? 31.5000 }},{{ $delivery->receiver?->profile?->longitude ?? 34.4667 }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 font-bold mr-1.5">🗺️ معاينة التسليم</a>
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-t border-slate-200 flex justify-end">
                            @if(Auth::user()->status === 'active')
                                <form method="POST" action="{{ route('listings.accept-delivery', $delivery->id) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-xs font-bold rounded-xl shadow-md shadow-indigo-100 text-white bg-indigo-600 hover:bg-indigo-700">
                                        قبول مهمة التوصيل
                                    </button>
                                </form>
                            @else
                                <button disabled class="inline-flex items-center px-4 py-2 border border-transparent text-xs font-bold rounded-xl text-white bg-slate-200 cursor-not-allowed">
                                    قبول مهمة التوصيل (الحساب غير موثق)
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- 3. سجل عمليات التوصيل المنجزة (الأرشيف) -->
    <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6">
        <h3 class="text-lg font-bold text-slate-900 mb-4">سجل العمليات التي قمت بتوصيلها بنجاح</h3>
        
        @php
            $myCompletedDeliveries = \App\Models\Listing::with(['user.profile', 'receiver.profile'])
                ->where('driver_id', Auth::user()->id)
                ->where('status', 'completed')
                ->orderBy('updated_at', 'desc')
                ->get();
        @endphp

        @if($myCompletedDeliveries->isEmpty())
            <p class="text-sm text-slate-400">لا توجد عمليات توصيل منجزة ومسجلة باسمك بعد.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-right">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500">اسم الوجبة</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500">من (المتبرع)</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500">إلى (الجمعية)</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500">وقت التوصيل الفعلي</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @foreach($myCompletedDeliveries as $delivery)
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900">{{ $delivery->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $delivery->user?->profile?->organization_name ?? $delivery->user?->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $delivery->receiver?->profile?->organization_name ?? $delivery->receiver?->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-400 font-medium">{{ $delivery->updated_at->format('Y-m-d H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 inline-flex text-[10px] leading-5 font-bold rounded-full bg-green-50 text-green-700 border border-green-200">مكتمل ومسلّم</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>