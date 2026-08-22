<!-- استدعاء مكتبة الخرائط العالمية المفتوحة Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

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

    <!-- 1. قسم تتبع الشحنات الحالية -->
    <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6">
        <h3 class="text-lg font-bold text-slate-900 mb-6">تتبع شحناتنا الحالية ومتابعة وصولها</h3>
        
        @php
            $myReservedListings = \App\Models\Listing::with(['user.profile', 'driver.profile'])
                ->where('receiver_id', Auth::user()->id)
                ->whereIn('status', ['reserved', 'picked_up'])
                ->get();
        @endphp

        @if($myReservedListings->isEmpty())
            <p class="text-sm text-gray-500">ليس لديكم أي وجبات محجوزة حالياً بانتظار الاستلام.</p>
        @else
            <div class="grid grid-cols-1 gap-6">
                @foreach($myReservedListings as $listing)
                    <div 
                        x-data="{ 
                            status: '{{ $listing->status }}',
                            driverId: '{{ $listing->driver_id }}',
                            driverName: '{{ $listing->driver?->name }}',
                            driverPhone: '{{ $listing->driver?->profile?->phone }}',
                            checkStatus() {
                                fetch('/api/listings/{{ $listing->id }}/status')
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.status === 'completed') {
                                            window.location.reload();
                                        }
                                        this.status = data.status;
                                        this.driverId = data.driver_id;
                                        this.driverName = data.driver_name;
                                        this.driverPhone = data.driver_phone;
                                    });
                            }
                        }"
                        x-init="setInterval(() => checkStatus(), 4000)"
                        class="border border-slate-100 rounded-2xl p-6 bg-slate-50/40 space-y-6"
                    >
                        
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center pb-4 border-b border-slate-200/60">
                            <div>
                                <h4 class="text-md font-extrabold text-slate-900">{{ $listing->title }}</h4>
                                <p class="text-xs text-slate-400 mt-1">الجهة المتبرعة: {{ $listing->user?->profile?->organization_name ?? $listing->user?->name }} | مكان الاستلام: {{ $listing->address }}</p>
                            </div>
                            
                            <div class="mt-4 md:mt-0">
                                <form method="POST" action="{{ route('listings.cancel', $listing->id) }}" onsubmit="return confirm('هل تريد إلغاء حجز هذه الوجبة وإعادة إتاحتها لجمعيات أخرى؟');">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold text-rose-500 hover:bg-rose-50 px-3 py-1.5 rounded-lg border border-rose-100 transition-all">
                                        إلغاء وحذف الحجز
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- معلومات المندوب -->
                        <div>
                            <div x-show="driverId" x-transition class="flex items-center justify-between p-4 bg-white border border-slate-100 rounded-xl" style="display: none;">
                                <div class="flex items-center space-x-3 space-x-reverse">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                                        🚚
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-400 font-bold">المندوب المكلف بالتوصيل</p>
                                        <p class="text-sm font-extrabold text-slate-900 mt-0.5" x-text="driverName"></p>
                                    </div>
                                </div>
                                <div class="text-left">
                                    <a :href="'tel:' + driverPhone" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-lg transition-colors">
                                        📞 اتصال: <span x-text="driverPhone"></span>
                                    </a>
                                </div>
                            </div>

                            <div x-show="!driverId" x-transition class="flex items-center space-x-3 space-x-reverse p-4 bg-white border border-slate-100 rounded-xl">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm animate-pulse">
                                    ⏳
                                </div>
                                <div>
                                    <p class="text-sm font-extrabold text-amber-700">بانتظار مندوب توصيل ليقبل الطلب</p>
                                    <p class="text-xs text-slate-400 mt-0.5">سيظهر اسم المندوب وهاتفه هنا فور قبوله للمهمة اللوجستية.</p>
                                </div>
                            </div>
                        </div>

                        <!-- الخط الزمني -->
                        <div class="relative py-6">
                            <div class="absolute left-4 right-4 top-1/2 -translate-y-1/2 h-1 bg-slate-100 rounded-full z-0"></div>
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 h-1 bg-emerald-500 rounded-full transition-all duration-700 z-0" 
                                 :style="'width: ' + (status === 'reserved' && !driverId ? '15%' : (status === 'reserved' && driverId ? '50%' : '100%')) + '; right: 1rem;'"></div>

                            <div class="relative z-10 flex justify-between items-center text-center">
                                <div class="flex flex-col items-center space-y-2">
                                    <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-xs shadow-md shadow-emerald-100">✓</div>
                                    <span class="text-[10px] font-bold text-slate-800">تم تأكيد الحجز</span>
                                </div>

                                <div class="flex flex-col items-center space-y-2">
                                    <div class="w-8 h-8 rounded-full transition-all duration-500 flex items-center justify-center font-bold text-xs shadow-md"
                                         :class="driverId ? 'bg-emerald-500 text-white shadow-emerald-100' : 'bg-amber-500 text-white animate-pulse'">
                                        <span x-text="driverId ? '✓' : '2'"></span>
                                    </div>
                                    <span class="text-[10px] font-bold" :class="driverId ? 'text-slate-800' : 'text-amber-600'">
                                        <span x-text="driverId ? 'المندوب جاهز للاستلام' : 'بانتظار قبول السائق'"></span>
                                    </span>
                                </div>

                                <div class="flex flex-col items-center space-y-2">
                                    <div class="w-10 h-10 rounded-full transition-all duration-500 flex items-center justify-center font-bold text-xs shadow-md"
                                         :class="status === 'picked_up' ? 'bg-indigo-600 text-white animate-bounce' : 'bg-slate-200 text-slate-400'">
                                        <span>🚚</span>
                                    </div>
                                    <span class="text-[10px] font-bold" :class="status === 'picked_up' ? 'text-indigo-600' : 'text-slate-400'">
                                        <span x-text="status === 'picked_up' ? 'المندوب في طريقه إليك الآن!' : 'قيد التوصيل للجمعية'"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- كود الأمان لتوثيق التسليم للمندوب -->
                        <div x-show="status === 'picked_up'" x-transition class="mt-4 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-center space-y-1" style="display: none;">
                            <p class="text-xs text-emerald-800 font-bold">🔐 رمز تأكيد تسليم الشحنة للمندوب</p>
                            <p class="text-3xl font-black text-emerald-600 tracking-widest">{{ $listing->verification_code }}</p>
                            <p class="text-[9px] text-emerald-500 font-semibold">يرجى تزويد المندوب بهذا الرمز عند وصوله وتسليمكم الوجبات ليتمكن من إنهاء مهمة التوصيل بنجاح [6].</p>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- 2. قسم تصفح وحجز الوجبات المتاحة حالياً -->
    <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6">
        <h3 class="text-lg font-bold text-slate-900 mb-2">التبرعات وفائض الأطعمة المتاحة حالياً</h3>
        <p class="text-xs text-slate-400 mb-4">عرض جغرافي حي على الخريطة التفاعلية للوجبات المتاحة حالياً حول منطقتك وسهولة حجزها.</p>

        @php
            $availableListings = \App\Models\Listing::with(['user.profile'])
                ->where('status', 'available')
                ->orderBy('created_at', 'desc')
                ->get();
        @endphp

        <!-- [تأمين الأبعاد الثابتة يدوياً لمنع الاختفاء التام للخريطة عند التجميع] [10] -->
        @if(!$availableListings->isEmpty())
            <div id="map" style="height: 320px; min-height: 320px; width: 100%;" class="rounded-2xl border border-slate-100 shadow-sm z-10 mb-6 relative"></div>
        @endif

        @if($availableListings->isEmpty())
            <div class="text-center py-12 border-2 border-dashed border-slate-200 rounded-2xl">
                <h3 class="mt-2 text-sm font-bold text-slate-800">لا يوجد فائض أطعمة متاح في هذه اللحظة</h3>
                <p class="mt-1 text-sm text-gray-500">سيتم تحديث هذه القائمة تلقائياً فور قيام أي متبرع بنشر فائض طعام جديد.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($availableListings as $availableListing)
                    <div id="listing-{{ $availableListing->id }}" class="listing-card-target border border-slate-100 rounded-2xl p-5 bg-gray-50 hover:shadow-md transition-all duration-300 flex flex-col justify-between scroll-mt-24">
                        <div class="space-y-3">
                            <div class="flex justify-between items-start">
                                <h4 class="text-md font-bold text-slate-950">{{ $availableListing->title }}</h4>
                                <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-green-100 text-green-800 border border-green-200">متاح للاستلام</span>
                            </div>
                            
                            <!-- شارة الملاءمة اللوجستية الذكية المحسوبة ديناميكياً بـ GPS -->
                            @php
                                $matchScore = $availableListing->calculateMatchScore(Auth::user());
                            @endphp
                            <div class="mt-1.5 flex items-center">
                                <span class="px-2 py-0.5 text-[9px] font-extrabold rounded-md flex items-center
                                    @if($matchScore >= 85) bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @elseif($matchScore >= 60) bg-amber-50 text-amber-700 border border-amber-200
                                    @else bg-slate-100 text-slate-600 border border-slate-200 @endif animate-pulse"
                                >
                                    🪄 ملاءمة لوجستية ذكية: {{ $matchScore }}% 
                                    @if($matchScore >= 85) (أولوية إنقاذ قصوى لقرب المسافة)
                                    @elseif($matchScore >= 60) (مناسب للاستلام والإنقاذ)
                                    @else (مستقر جغرافياً وزمنياً) @endif
                                </span>
                            </div>

                            <p class="text-xs text-slate-400 mt-2">الجهة المتبرعة: {{ $availableListing->user?->profile?->organization_name ?? $availableListing->user?->name }}</p>
                            <p class="text-sm text-gray-700 mt-3 whitespace-pre-line leading-relaxed">{{ $availableListing->description ?? 'لا يوجد وصف إضافي.' }}</p>
                            
                            <div class="mt-4 space-y-2 text-xs text-slate-500">
                                <div class="flex items-center">📦 الكمية المطلوبة: {{ $availableListing->quantity }}</div>
                                <div class="flex items-center">🕒 صالح حتى: {{ $availableListing->expiry_time->format('Y-m-d H:i') }} ({{ $availableListing->expiry_time->diffForHumans() }})</div>
                                <div class="flex items-center">📍 عنوان التواجد: {{ $availableListing->address }}</div>
                            </div>
                        </div>

                        <!-- أزرار الإجراءات وحجز الوجبة مع محدد الموقع التفاعلي ثنائي الاتجاه -->
                        <div class="mt-5 pt-4 border-t border-slate-200 flex justify-between items-center">
                            <button type="button" 
                                    onclick="focusOnMapListing({{ $availableListing->id }}, {{ $availableListing->latitude ?? 31.5000 }}, {{ $availableListing->longitude ?? 34.4667 }})" 
                                    class="inline-flex items-center text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors"
                            >
                                🗺️ تحديد على الخريطة
                            </button>

                            @if(Auth::user()->status === 'active')
                                <form method="POST" action="{{ route('listings.reserve', $availableListing->id) }}">
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

    <!-- 3. سجل المواد والوجبات المستلمة سابقاً -->
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
                            <th class="px-6 py-4 text-xs font-bold text-slate-500">الإيصال والتوثيق</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @foreach($myCompletedListings as $completedListing)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800">{{ $completedListing->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-medium">
                                    <div class="font-bold text-slate-700">{{ $completedListing->user?->profile?->organization_name ?? $completedListing->user?->name }}</div>
                                    @if($completedListing->driver_id)
                                        <div class="text-[11px] text-indigo-600 font-bold mt-1.5 flex items-center">
                                            🚚 المندوب: {{ $completedListing->driver?->name }} (هاتف: {{ $completedListing->driver?->profile?->phone ?? 'غير متوفر' }})
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-400 font-medium">{{ $completedListing->quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-400 font-medium">{{ $completedListing->updated_at->format('Y-m-d H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 inline-flex text-[10px] leading-5 font-bold rounded-full bg-blue-50 text-blue-700 border border-blue-200">تم التسليم بنجاح</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-bold">
                                    <a href="{{ route('listings.receipt', $completedListing->id) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition-colors">
                                        📋 إيصال الاستلام
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

<!-- كود جافا سكريبت لتشغيل الخريطة التفاعلية الحية وتنزيل نقاط الـ GPS -->
@if(!$availableListings->isEmpty())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // تأمين ارتفاع الخريطة ليعمل مع Leaflet بنجاح
        var map = L.map('map').setView([31.5000, 34.4667], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var mapMarkers = {};

        var listings = [
            @foreach($availableListings as $listing)
            {
                id: {{ $listing->id }},
                title: "{{ $listing->title }}",
                quantity: "{{ $listing->quantity }}",
                lat: {{ $listing->latitude ?? 31.5000 }},
                lng: {{ $listing->longitude ?? 34.4667 }},
                donor: "{{ $listing->user?->profile?->organization_name ?? $listing->user?->name }}"
            },
            @endforeach
        ];

        listings.forEach(function(listing) {
            var marker = L.marker([listing.lat, listing.lng]).addTo(map);
            mapMarkers[listing.id] = marker;

            var popupContent = `
                <div class="text-right space-y-1.5" dir="rtl" style="font-family: 'Cairo', sans-serif;">
                    <strong class="text-slate-900 block text-xs font-bold">${listing.title}</strong>
                    <p class="text-[10px] text-slate-500 font-semibold m-0">الكمية: ${listing.quantity}</p>
                    <p class="text-[10px] text-emerald-600 font-bold m-0">المتبرع: ${listing.donor}</p>
                    <a href="#listing-${listing.id}" class="text-[9px] font-bold text-indigo-600 hover:text-indigo-800 hover:underline block mt-1.5">← تصفح الوجبة وحجزها الآن</a>
                </div>
            `;
            marker.bindPopup(popupContent);
        });

        window.focusOnMapListing = function(listingId, lat, lng) {
            document.getElementById('map').scrollIntoView({ behavior: 'smooth', block: 'center' });
            map.setView([lat, lng], 14, { animate: true, duration: 1.5 });
            if (mapMarkers[listingId]) {
                setTimeout(function() {
                    mapMarkers[listingId].openPopup();
                }, 800);
            }
        };
    });
</script>
@endif