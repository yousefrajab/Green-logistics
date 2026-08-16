<div class="space-y-6">
    
    <!-- عرض رسائل النجاح أو الأخطاء عند الحجز أو الإلغاء -->
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

    <!-- 1. قسم طلباتنا المحجوزة الحالية (بانتظار الاستلام) -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100 p-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">طلباتنا المحجوزة (بانتظار الاستلام)</h3>
        
        @php
            $myReservedListings = \App\Models\Listing::with(['user.profile', 'driver.profile'])
                ->where('receiver_id', Auth::user()->id)
                ->whereIn('status', ['reserved', 'picked_up'])
                ->get();
        @endphp

        @if($myReservedListings->isEmpty())
            <p class="text-sm text-gray-500">ليس لديكم أي وجبات محجوزة حالياً بانتظار الاستلام.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-right">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">عنوان الوجبة</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">المتبرع</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">العنوان ومكان الاستلام</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">الحالة</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($myReservedListings as $listing)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $listing->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $listing->user?->profile?->organization_name ?? $listing->user?->name ?? 'متبرع غير معروف' }}
                                    
                                    <!-- دمج بيانات المندوب وهاتفه لتنسيق الاستلام الفعلي للجمعية -->
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $listing->address }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($listing->status === 'reserved')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">بانتظار مندوب للتوصيل</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">قيد التوصيل الآن مع المندوب</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <form method="POST" action="{{ route('listings.cancel', $listing->id) }}" onsubmit="return confirm('هل تريد إلغاء حجز هذه الوجبة وإعادة إتاحتها لجمعيات أخرى؟');">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900">إلغاء الحجز</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- 2. قسم تصفح وحجز الوجبات المتاحة حالياً على المنصة -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100 p-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">التبرعات وفائض الأطعمة المتاحة حالياً</h3>
        
        @php
            $availableListings = \App\Models\Listing::with(['user.profile'])
                ->where('status', 'available')
                ->orderBy('created_at', 'desc')
                ->get();
        @endphp

        @if($availableListings->isEmpty())
            <div class="text-center py-12 border-2 border-dashed border-gray-200 rounded-lg">
                <h3 class="mt-2 text-sm font-medium text-gray-900">لا يوجد فائض أطعمة متاح في هذه اللحظة</h3>
                <p class="mt-1 text-sm text-gray-500">سيتم تحديث هذه القائمة تلقائياً فور قيام أي متبرع بنشر فائض طعام جديد.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($availableListings as $listing)
                    <div class="border border-gray-100 rounded-lg p-5 bg-gray-50 hover:shadow-md transition-shadow flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start">
                                <h4 class="text-md font-bold text-gray-900">{{ $listing->title }}</h4>
                                <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800">متاح للاستلام</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">الجهة المتبرعة: {{ $listing->user?->profile?->organization_name ?? $listing->user?->name }}</p>
                            <p class="text-sm text-gray-700 mt-3 whitespace-pre-line">{{ $listing->description ?? 'لا يوجد وصف إضافي.' }}</p>
                            
                            <div class="mt-4 space-y-2 text-xs text-gray-500">
                                <div class="flex items-center">
                                    <span class="font-semibold ml-1">الكمية المطلوبة:</span> {{ $listing->quantity }}
                                </div>
                                <div class="flex items-center">
                                    <span class="font-semibold ml-1">صالح حتى:</span> {{ $listing->expiry_time->format('Y-m-d H:i') }} ({{ $listing->expiry_time->diffForHumans() }})
                                </div>
                                <div class="flex items-center">
                                    <span class="font-semibold ml-1">عنوان التواجد:</span> {{ $listing->address }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-t border-gray-200 flex justify-end">
                            @if(Auth::user()->status === 'active')
                                <form method="POST" action="{{ route('listings.reserve', $listing->id) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        حجز وتنسيق الاستلام
                                    </button>
                                </form>
                            @else
                                <button disabled class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-300 cursor-not-allowed">
                                    حجز وتنسيق الاستلام (الحساب غير موثق)
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- 3. سجل المواد والوجبات المستلمة سابقاً (الأرشيف للجمعية) -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100 p-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">سجل الوجبات والمواد المستلمة سابقاً</h3>
        
        @php
            // تعديل الاستعلام هنا ليشمل جلب بيانات المندوب وملفه الشخصي في الأرشيف أيضاً
            $myCompletedListings = \App\Models\Listing::with(['user.profile', 'driver.profile'])
                ->where('receiver_id', Auth::user()->id)
                ->where('status', 'completed')
                ->orderBy('updated_at', 'desc')
                ->get();
        @endphp

        @if($myCompletedListings->isEmpty())
            <p class="text-sm text-gray-500">لا توجد عمليات استلام مكتملة وموثقة في سجلكم بعد.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-right">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500">الوجبة المستلمة</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500">الجهة المتبرعة والمندوب</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500">الكمية الموفرة</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500">تاريخ الاستلام الفعلي</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($myCompletedListings as $listing)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $listing->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>{{ $listing->user?->profile?->organization_name ?? $listing->user?->name ?? 'متبرع غير معروف' }}</div>
                                    
                                    <!-- إظهار بيانات المندوب الذي قام بالتوصيل بنجاح في سجل الأرشيف للجمعية -->
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $listing->quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $listing->updated_at->format('Y-m-d H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">تم التسليم بنجاح</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>