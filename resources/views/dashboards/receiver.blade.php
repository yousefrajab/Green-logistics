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

    <!-- 1. طلباتنا المحجوزة الحالية (بانتظار الاستلام) -->
    <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6">
        <h3 class="text-lg font-bold text-slate-900 mb-4">طلباتنا المحجوزة (بانتظار الاستلام)</h3>
        
        @php
            $myReservedListings = \App\Models\Listing::with(['user.profile', 'driver.profile'])
                ->where('receiver_id', Auth::user()->id)
                ->whereIn('status', ['reserved', 'picked_up'])
                ->get();
        @endphp

        @if($myReservedListings->isEmpty())
            <p class="text-sm text-slate-400">ليس لديكم أي وجبات محجوزة حالياً بانتظار الاستلام.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-right">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500">عنوان الوجبة</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500">المتبرع والمندوب</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500">مقر الاستلام</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500">الحالة</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @foreach($myReservedListings as $listing)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800">{{ $listing->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    <div class="font-semibold text-slate-700">{{ $listing->user?->profile?->organization_name ?? $listing->user?->name }}</div>
                                    @if($listing->driver_id)
                                        <div class="text-[11px] text-indigo-600 font-bold mt-1.5 flex items-center">
                                            🚚 المندوب: {{ $listing->driver?->name }} (هاتف: {{ $listing->driver?->profile?->phone ?? 'غير متوفر' }})
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-400 font-medium">{{ $listing->address }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($listing->status === 'reserved')
                                        <span class="px-2.5 py-1 inline-flex text-[10px] leading-5 font-bold rounded-full bg-yellow-50 text-yellow-700 border border-yellow-200">بانتظار مندوب للتوصيل</span>
                                    @else
                                        <span class="px-2.5 py-1 inline-flex text-[10px] leading-5 font-bold rounded-full bg-blue-50 text-blue-700 border border-blue-200">قيد التوصيل الآن مع المندوب</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-bold">
                                    <form method="POST" action="{{ route('listings.cancel', $listing->id) }}" onsubmit="return confirm('هل تريد إلغاء حجز هذه الوجبة وإعادة إتاحتها لجمعيات أخرى؟');">
                                        @csrf
                                        <button type="submit" class="text-rose-600 hover:text-rose-900 transition-colors">إلغاء الحجز</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- 2. التبرعات وفائض الأطعمة المتاحة حالياً (كروت مذهلة وتفاعلية) -->
    <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6">
        <h3 class="text-lg font-bold text-slate-900 mb-4">التبرعات وفائض الأطعمة المتاحة حالياً</h3>
        
        @php
            $availableListings = \App\Models\Listing::with(['user.profile'])
                ->where('status', 'available')
                ->orderBy('created_at', 'desc')
                ->get();
        @endphp

        @if($availableListings->isEmpty())
            <div class="text-center py-16 border-2 border-dashed border-slate-100 rounded-2xl">
                <h3 class="text-sm font-bold text-slate-800">لا يوجد فائض أطعمة متاح في هذه اللحظة</h3>
                <p class="mt-1 text-xs text-slate-400">سيتم تحديث هذه القائمة تلقائياً فور قيام أي متبرع بنشر فائض طعام جديد.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($availableListings as $listing)
                    <div class="border border-slate-100 rounded-2xl p-6 bg-slate-50/50 hover:shadow-lg transition-all duration-300 flex flex-col justify-between hover:-translate-y-0.5">
                        <div class="space-y-3">
                            <div class="flex justify-between items-start">
                                <h4 class="text-md font-bold text-slate-950">{{ $listing->title }}</h4>
                                <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">متاح للاستلام</span>
                            </div>
                            <p class="text-xs text-slate-400 font-medium">الجهة المتبرعة: {{ $listing->user?->profile?->organization_name ?? $listing->user?->name }}</p>
                            <p class="text-sm text-slate-600 whitespace-pre-line leading-relaxed">{{ $listing->description ?? 'لا يوجد وصف إضافي.' }}</p>
                            
                            <div class="mt-4 pt-3 border-t border-slate-200/60 space-y-2 text-xs text-slate-500 font-semibold">
                                <div class="flex items-center">📦 الكمية: {{ $listing->quantity }}</div>
                                <div class="flex items-center">🕒 صالح حتى: {{ $listing->expiry_time->format('Y-m-d H:i') }} ({{ $listing->expiry_time->diffForHumans() }})</div>
                                <div class="flex items-center">📍 عنوان التواجد: {{ $listing->address }}</div>
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-t border-slate-100 flex justify-end">
                            @if(Auth::user()->status === 'active')
                                <form method="POST" action="{{ route('listings.reserve', $listing->id) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-xs font-bold rounded-xl shadow-md shadow-emerald-100 text-white bg-emerald-600 hover:bg-emerald-700 transition-colors">
                                        حجز وتنسيق الاستلام
                                    </button>
                                </form>
                            @else
                                <button disabled class="inline-flex items-center px-4 py-2 border border-transparent text-xs font-bold rounded-xl text-white bg-slate-200 cursor-not-allowed">
                                    حجز وتنسيق الاستلام (الحساب غير موثق)
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- 3. سجل المواد والوجبات المستلمة سابقاً (الأرشيف) -->
    <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6">
        <h3 class="text-lg font-bold text-slate-900 mb-4">سجل الوجبات والمواد المستلمة سابقاً</h3>
        
        @php
            $myCompletedListings = \App\Models\Listing::with(['user.profile', 'driver.profile'])
                ->where('receiver_id', Auth::user()->id)
                ->where('status', 'completed')
                ->orderBy('updated_at', 'desc')
                ->get();
        @endphp

        @if($myCompletedListings->isEmpty())
            <p class="text-sm text-slate-400">لا توجد عمليات استلام مكتملة وموثقة في سجلكم بعد.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-right">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500">الوجبة المستلمة</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500">الجهة المتبرعة والمندوب</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500">الكمية الموفرة</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500">تاريخ الاستلام الفعلي</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @foreach($myCompletedListings as $listing)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800">{{ $listing->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                    <div class="font-semibold text-slate-700">{{ $listing->user?->profile?->organization_name ?? $listing->user?->name }}</div>
                                    @if($listing->driver_id)
                                        <div class="text-[11px] text-indigo-600 font-bold mt-1.5 flex items-center">
                                            🚚 المندوب الموصل: {{ $listing->driver?->name }} (هاتف: {{ $listing->driver?->profile?->phone ?? 'غير متوفر' }})
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-400 font-medium">{{ $listing->quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-400 font-medium">{{ $listing->updated_at->format('Y-m-d H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 inline-flex text-[10px] leading-5 font-bold rounded-full bg-blue-50 text-blue-700 border border-blue-200">تم التسليم بنجاح</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>