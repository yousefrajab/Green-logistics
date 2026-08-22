<div class="space-y-6" dir="rtl" x-data="greenReportComponent()">
    
    <!-- إشعار النجاح عند إضافة أو تعديل أو تراجع عن إعلان -->
    @if(session('success'))
        <div class="bg-emerald-50 border-r-4 border-emerald-500 p-4 rounded-xl shadow-sm shadow-emerald-50/50">
            <div class="flex items-center">
                <div class="flex-shrink-0 text-emerald-500">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <p class="mr-3 text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border-r-4 border-rose-500 p-4 rounded-xl shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0 text-rose-500">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <p class="mr-3 text-sm font-semibold text-rose-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- [تكامل تقرير الأثر البيئي والاستدامة بالذكاء الاصطناعي - AI Green CSR Card] [4] -->
    <div class="bg-gradient-to-r from-emerald-950 via-slate-900 to-slate-950 p-6 rounded-2xl border border-emerald-900/40 shadow-lg text-white mb-6 flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
        
        <div class="space-y-1 relative z-10">
            <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">مبادرة الفنادق الخضراء المستدامة</span>
            <h3 class="text-md font-bold font-extrabold text-slate-800">تقرير الأثر المسؤول ومكافحة هدر الغذاء (AI Green Report)</h3>
            <p class="text-xs text-slate-800 max-w-2xl">دع الذكاء الاصطناعي يحلل إحصائيات تبرعاتك الحقيقية ويصيغ لك تقرير المسؤولية المجتمعية (CSR) الخاص بمنشأتكم فوراً لعرضه ومشاركته أمام عملائكم وحساباتكم الاجتماعية بضغطة زر [4]!</p>
        </div>
        <div class="relative z-10 shrink-0">
            <button type="button" @click="generateReport()" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-950/40 transition-all hover:scale-105">
                🪄 توليد تقرير الاستدامة والـ CSR
            </button>
        </div>
    </div>

    <!-- كروت الإحصائيات الحديثة بتأثيرات تمرير ناعمة وبوجود زر التقرير اللطيف المدمج -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- كرت إجمالي التبرعات -->
        <div class="bg-white overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 rounded-2xl border border-slate-100 p-6 flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-slate-400">إجمالي الوجبات المتبرع بها</p>
                <p class="text-3xl font-extrabold text-slate-800">{{ Auth::user()->listings->count() }}</p>
            </div>
            <div class="p-3.5 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100/50">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
        </div>

        <!-- كرت وجبات محجوزة -->
        <div class="bg-white overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 rounded-2xl border border-slate-100 p-6 flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-slate-400">وجبات محجوزة حالياً</p>
                <p class="text-3xl font-extrabold text-amber-600">{{ Auth::user()->listings->whereIn('status', ['reserved', 'picked_up'])->count() }}</p>
            </div>
            <div class="p-3.5 rounded-2xl bg-amber-50 text-amber-600 border border-amber-100/50">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- كرت عمليات مكتملة المطور والمدمج به ذكاء تقارير الاستدامة -->
        <div class="bg-white overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 rounded-2xl border border-slate-100 p-6 flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-slate-400">عمليات توصيل مكتملة</p>
                <p class="text-3xl font-extrabold text-blue-600">{{ Auth::user()->listings->where('status', 'completed')->count() }}</p>
                
                @if(Auth::user()->listings->where('status', 'completed')->count() > 0)
                    <button type="button" @click="generateReport()" class="text-[10px] font-bold text-emerald-600 hover:text-emerald-800 flex items-center mt-1 animate-pulse focus:outline-none">
                        🪄 عرض تقرير الاستدامة والـ CSR
                    </button>
                @endif
            </div>
            <div class="p-3.5 rounded-2xl bg-blue-50 text-blue-600 border border-blue-100/50">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- جدول البيانات المنسق بالكامل -->
    <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6">
        <div class="sm:flex sm:items-center sm:justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-slate-900">إعلانات الفائض الحالية</h3>
                <p class="mt-1 text-xs text-slate-400">قائمة بالأطعمة والسلع التي قمت بالإعلان عنها وحالة كل منها من قاعدة البيانات.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                @if(Auth::user()->status === 'active')
                    <a href="{{ route('listings.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-xs font-bold rounded-xl shadow-md shadow-emerald-100 text-white bg-emerald-600 hover:bg-emerald-700 transition-all">
                        + الإعلان عن فائض طعام
                    </a>
                @else
                    <button disabled class="inline-flex items-center justify-center px-4 py-2.5 border border-transparent text-xs font-bold rounded-xl text-white bg-slate-200 cursor-not-allowed">
                        + الإعلان عن فائض طعام (الحساب غير موثق)
                    </button>
                @endif
            </div>
        </div>

        @if(Auth::user()->listings->isEmpty())
            <div class="text-center py-16 border-2 border-dashed border-slate-100 rounded-2xl">
                <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="mt-4 text-sm font-bold text-slate-800">لا يوجد إعلانات فائض حالية</h3>
                <p class="mt-1 text-sm text-gray-500">ابدأ بمشاركة الفائض للمساهمة في حفظ النعمة ومساعدة المحتاجين.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-right">
                    <thead class="bg-slate-50/50 rounded-xl">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">عنوان الإعلان</th>
                            <th scope="col" class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">الكمية</th>
                            <th scope="col" class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">التصنيف</th>
                            <th scope="col" class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">صالح لغاية</th>
                            <th scope="col" class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">الحالة</th>
                            <th scope="col" class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">تاريخ النشر</th>
                            <th scope="col" class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @foreach(Auth::user()->listings as $listing)
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-slate-800">{{ $listing->title }}</div>
                                    <div class="text-xs text-slate-400 truncate max-w-xs mt-0.5">{{ $listing->description ?? 'بدون وصف إضافي' }}</div>
                                    
                                    @if($listing->driver_id)
                                        <div class="text-[11px] text-indigo-600 font-semibold mt-1.5 flex items-center">
                                            🚚 المندوب: {{ $listing->driver?->name }} (هاتف: {{ $listing->driver?->profile?->phone ?? 'غير متوفر' }})
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 font-medium">
                                    {{ $listing->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-bold">
                                    @if($listing->category === 'cooked') <span class="text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg">مطبوخ</span>
                                    @elseif($listing->category === 'dry') <span class="text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg">جاف ومعلب</span>
                                    @elseif($listing->category === 'fresh') <span class="text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">طازج</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 font-medium">
                                    {{ $listing->expiry_time->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($listing->status === 'available')
                                        <span class="px-2.5 py-1 inline-flex text-[10px] leading-5 font-bold rounded-full bg-green-50 text-green-700 border border-green-200">متاح للاستلام</span>
                                    @elseif($listing->status === 'reserved')
                                        <span class="px-2.5 py-1 inline-flex text-[10px] leading-5 font-bold rounded-full bg-yellow-50 text-yellow-700 border border-yellow-200">محجوز حالياً</span>
                                    @elseif($listing->status === 'picked_up')
                                        <span class="px-2.5 py-1 inline-flex text-[10px] leading-5 font-bold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200">جاري التوصيل</span>
                                    @elseif($listing->status === 'completed')
                                        <span class="px-2.5 py-1 inline-flex text-[10px] leading-5 font-bold rounded-full bg-blue-50 text-blue-700 border border-blue-200">تم التوصيل</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-400 font-medium">
                                    {{ $listing->created_at->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-bold flex items-center space-x-3 space-x-reverse h-full mt-2.5">
                                    @if($listing->status === 'available')
                                        <a href="{{ route('listings.edit', $listing->id) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors">تعديل</a>
                                        <form method="POST" action="{{ route('listings.destroy', $listing->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا الإعلان نهائياً؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-900 transition-colors">حذف</button>
                                        </form>
                                    @elseif(in_array($listing->status, ['reserved', 'picked_up']))
                                        <form method="POST" action="{{ route('listings.cancel', $listing->id) }}" onsubmit="return confirm('هل تريد التراجع عن منح هذه الشحنة للجمعية الحالية وإعادتها متاحة للجميع؟');">
                                            @csrf
                                            <button type="submit" class="text-amber-600 hover:text-amber-900 transition-colors">تراجع وإلغاء</button>
                                        </form>
                                    @elseif($listing->status === 'completed')
                                        <a href="{{ route('listings.receipt', $listing->id) }}" target="_blank" class="text-emerald-600 hover:text-emerald-900 transition-colors">
                                            📋 تحميل الإيصال
                                        </a>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- =========================================================
         [بوابة المطورين والربط البرمجي للكاشير - Developer & API Portal] [1, 8]
    ========================================================== -->
    {{-- <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6 mt-6">
        <h3 class="text-lg font-bold text-slate-900 mb-2">🔑 بوابة المطورين ومفاتيح الربط الإلكتروني (API Portal)</h3>
        <p class="text-xs text-slate-400 mb-6">قم بتوليد رموز وصول آمنة (API Tokens) لربط نظام كاشير الفندق أو المطعم (POS System) بـ جود لنشر الفائض آلياً [1, 8].</p>

        <!-- عرض الرمز المولد لمرة واحدة فقط لأسباب أمنية شديدة -->
        @if(session('success_token'))
            <div class="bg-emerald-50 border border-emerald-100 p-5 rounded-xl mb-6 space-y-2">
                <p class="text-xs font-bold text-emerald-800">🚨 تنبيه أمني هام جداً:</p>
                <p class="text-xs text-slate-600">هذا هو رمز الوصول الآمن الخاص بك لربط نظام الكاشير الخارجي. **قم بنسخه وحفظه الآن في مكان آمن**؛ فلن يظهر لك هذا الرمز مرة أخرى مطلقاً حمايةً لخصوصية حسابك [8]:</p>
                <div class="flex items-center space-x-2 space-x-reverse pt-2">
                    <input type="text" readonly value="{{ session('success_token') }}" id="api-token-input" class="bg-white border border-slate-200 text-slate-800 font-mono text-xs rounded-xl py-2 px-4 flex-grow select-all">
                    <button type="button" onclick="navigator.clipboard.writeText('{{ session('success_token') }}'); alert('🟢 تم نسخ الرمز بنجاح!')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition-all">نسخ الرمز</button>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
            <!-- فورم توليد رمز جديد -->
            <form method="POST" action="{{ route('donor.tokens.generate') }}" class="space-y-4">
                @csrf
                <div>
                    <x-input-label for="token_name" :value="__('اسم مفتاح الربط (مثال: كاشير بوفيه الغداء)')" class="text-xs font-bold text-slate-700" />
                    <x-text-input id="token_name" name="token_name" type="text" class="block mt-1 w-full" required placeholder="اكتب اسماً تعريفياً للرمز" />
                    <x-input-error :messages="$errors->get('token_name')" class="mt-2" />
                </div>
                <x-primary-button class="bg-emerald-600 hover:bg-emerald-700 py-2 px-6 rounded-xl text-xs font-bold">
                    ⚙️ توليد مفتاح ربط آمن (Token)
                </x-primary-button>
            </form>

            <!-- معلومات توثيق الـ API للمبرمجين -->
            <div class="p-5 bg-slate-50 border border-slate-100 rounded-2xl text-xs text-slate-600 space-y-3 leading-relaxed">
                <h4 class="font-bold text-slate-900">📄 دليل التوثيق السريع لربط الـ API:</h4>
                <p>قم بإرسال طلب من نوع **`POST`** إلى المسار التالي للربط المباشر [1]:</p>
                <code class="block bg-slate-950 text-emerald-400 p-2.5 rounded-xl font-mono text-[10px] break-all">POST: http://127.0.0.1:8000/api/v1/listings</code>
                <p>تأكد من تمرير المفتاح المولد كـ **`Bearer Token`** في الـ Headers الخاص بالطلب لحماية وأمان النشر [8]!</p>
            </div>
        </div>
    </div> --}}

    <!-- المودال الزجاجي الفخم لعرض تقرير الاستدامة المولد (AI CSR Modal Display) -->
    <div x-show="openReport" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="openReport = false" class="bg-white rounded-3xl max-w-2xl w-full border border-slate-100 p-8 shadow-2xl relative text-right">
            
            <div x-show="loadingReport" class="flex flex-col items-center justify-center space-y-4 py-12">
                <div class="w-10 h-10 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin"></div>
                <p class="text-xs font-bold text-slate-800 animate-pulse">جاري تحليل الأثر اللوجستي والبيئي الحقيقي لتبرعاتكم...</p>
            </div>

            <div x-show="!loadingReport" class="space-y-6">
                <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                    <h4 class="text-lg font-bold text-slate-900 flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ml-2 animate-pulse"></span>
                        تقرير الأثر البيئي والمسؤولية المجتمعية (CSR)
                    </h4>
                    <button type="button" @click="openReport = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-emerald-50/50 border border-emerald-100/50 p-4 rounded-xl text-center">
                        <p class="text-[10px] text-emerald-800 font-bold">💨 غاز ثاني أكسيد الكربون (CO2) المحفوظ</p>
                        <p class="text-lg font-extrabold text-emerald-600 mt-1" x-text="co2"></p>
                    </div>
                    <div class="bg-blue-50/50 border border-blue-100/50 p-4 rounded-xl text-center">
                        <p class="text-[10px] text-blue-800 font-bold">💧 مياه عذبة تم توفيرها في سلاسل الإنتاج</p>
                        <p class="text-lg font-extrabold text-blue-600 mt-1" x-text="water"></p>
                    </div>
                </div>

                <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl">
                    <p class="text-xs text-slate-700 leading-relaxed font-semibold whitespace-pre-line text-justify animate-fade-in" x-text="reportText"></p>
                </div>

                <div class="flex justify-between items-center pt-4 border-t border-slate-100">
                    <a href="{{ route('donor.green-report.print') }}" target="_blank" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-50 transition-all hover:scale-105">
                        🖨️ طباعة وتحميل الشهادة الرسمية (PDF)
                    </a>
                    <button type="button" @click="openReport = false" class="text-xs font-bold text-slate-500 hover:text-slate-800">
                        إإغلاق النافذة
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    function greenReportComponent() {
        return {
            openReport: false,
            loadingReport: false,
            co2: '',
            water: '',
            reportText: '',
            generateReport() {
                this.loadingReport = true;
                this.openReport = true;
                
                var self = this;
                
                fetch('{{ route('donor.green-report') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('فشلت عملية الاتصال بخدمة توليد التقارير البيئية.');
                    }
                    return response.json();
                })
                .then(function(data) {
                    self.co2 = data.co2_saved;
                    self.water = data.water_saved;
                    self.reportText = data.report_text;
                })
                .catch(function(error) {
                    console.error('Error generating report:', error);
                    self.reportText = 'عذراً، فشل الاتصال بخدمة توليد التقارير البيئية والمسؤولية المجتمعية.';
                })
                .finally(function() {
                    self.loadingReport = false;
                });
            }
        }
    }
</script>