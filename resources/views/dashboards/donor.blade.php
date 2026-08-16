<div class="space-y-6">
    
    <!-- إشعار النجاح عند إضافة أو تعديل أو حذف أو تراجع عن إعلان -->
    @if(session('success'))
        <div class="bg-green-50 border-r-4 border-green-400 p-4 rounded-md shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="mr-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border-r-4 border-red-400 p-4 rounded-md shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="mr-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- كروت الإحصائيات الحقيقية والديناميكية -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- كرت إجمالي التبرعات -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100 p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 truncate">إجمالي الوجبات المتبرع بها</p>
                <p class="mt-1 text-3xl font-semibold text-gray-900">{{ Auth::user()->listings->count() }}</p>
            </div>
            <div class="p-3 rounded-full bg-green-50 text-green-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
        </div>

        <!-- كرت طلبات بانتظار الاستلام -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100 p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 truncate">وجبات محجوزة حالياً</p>
                <p class="mt-1 text-3xl font-semibold text-amber-600">{{ Auth::user()->listings->whereIn('status', ['reserved', 'picked_up'])->count() }}</p>
            </div>
            <div class="p-3 rounded-full bg-amber-50 text-amber-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- كرت الوجبات التي تم إنقاذها بنجاح -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100 p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 truncate">عمليات توصيل مكتملة</p>
                <p class="mt-1 text-3xl font-semibold text-blue-600">{{ Auth::user()->listings->where('status', 'completed')->count() }}</p>
            </div>
            <div class="p-3 rounded-full bg-blue-50 text-blue-600">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- قسم الإجراءات وجدول البيانات -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100 p-6">
        <div class="sm:flex sm:items-center sm:justify-between mb-6">
            <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900">إعلانات الفائض الحالية</h3>
                <p class="mt-1 text-sm text-gray-500">قائمة بالأطعمة والسلع التي قمت بالإعلان عنها وحالة كل منها من قاعدة البيانات.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                @if(Auth::user()->status === 'active')
                    <a href="{{ route('listings.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        + الإعلان عن فائض طعام
                    </a>
                @else
                    <button disabled class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-300 cursor-not-allowed">
                        + الإعلان عن فائض طعام
                    </button>
                @endif
            </div>
        </div>

        @if(Auth::user()->listings->isEmpty())
            <div class="text-center py-12 border-2 border-dashed border-gray-200 rounded-lg">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">لا يوجد إعلانات فائض حالية</h3>
                <p class="mt-1 text-sm text-gray-500">ابدأ بمشاركة الفائض للمساهمة في حفظ النعمة ومساعدة المحتاجين.</p>
            </div>
        @else
            <!-- الجدول الديناميكي الفاخر مع عمود الإجراءات ومتابعة السائقين -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-right">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">عنوان الإعلان</th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">الكمية</th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">التصنيف</th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">صالح لغاية</th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ النشر</th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach(Auth::user()->listings as $listing)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $listing->title }}</div>
                                    <div class="text-xs text-gray-500 truncate max-w-xs">{{ $listing->description ?? 'بدون وصف إضافي' }}</div>
                                    
                                    <!-- دمج بيانات المندوب وهاتفه لتنسيق الاستلام -->
                                    @if($listing->driver_id)
                                        <div class="text-xs text-indigo-600 font-semibold mt-1 flex items-center">
                                            <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V15a1 1 0 01-1 1h-2" />
                                            </svg>
                                            المندوب: {{ $listing->driver?->name }} (هاتف: {{ $listing->driver?->profile?->phone ?? 'غير متوفر' }})
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $listing->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($listing->category === 'cooked') <span class="text-amber-600 font-medium">مطبوخ</span>
                                    @elseif($listing->category === 'dry') <span class="text-blue-600 font-medium">جاف ومعلب</span>
                                    @elseif($listing->category === 'fresh') <span class="text-green-600 font-medium">طازج</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $listing->expiry_time->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($listing->status === 'available')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">متاح للاستلام</span>
                                    @elseif($listing->status === 'reserved')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">محجوز حالياً</span>
                                    @elseif($listing->status === 'picked_up')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">جاري التوصيل</span>
                                    @elseif($listing->status === 'completed')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">تم التوصيل</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $listing->created_at->diffForHumans() }}
                                </td>
                                
                                <!-- حوكمة إجراءات التعديل والحذف والتراجع للمتبرع -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center space-x-3 space-x-reverse">
                                    @if($listing->status === 'available')
                                        <a href="{{ route('listings.edit', $listing->id) }}" class="text-indigo-600 hover:text-indigo-900">تعديل</a>
                                        <form method="POST" action="{{ route('listings.destroy', $listing->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا الإعلان نهائياً؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">حذف</button>
                                        </form>
                                    @elseif(in_array($listing->status, ['reserved', 'picked_up']))
                                        <form method="POST" action="{{ route('listings.cancel', $listing->id) }}" onsubmit="return confirm('هل تريد التراجع عن منح هذه الشحنة للجمعية الحالية وإعادتها متاحة للجميع؟');">
                                            @csrf
                                            <button type="submit" class="text-amber-600 hover:text-amber-900">تراجع وإلغاء</button>
                                        </form>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>