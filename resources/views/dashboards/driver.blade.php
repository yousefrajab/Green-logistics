<div class="space-y-6">

    <!-- عرض الإشعارات -->
    @if(session('success'))
        <div class="bg-green-50 border-r-4 border-green-400 p-4 rounded-md shadow-sm">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border-r-4 border-red-400 p-4 rounded-md shadow-sm">
            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    <!-- 1. الشحنات النشطة الحالية (التي قبلها هذا المندوب وجاري العمل عليها) -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100 p-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">شحناتي النشطة الحالية</h3>
        
        @php
            // جلب الشحنات التي يعمل عليها هذا المندوب حالياً ولم تكتمل بعد
            $myActiveDeliveries = \App\Models\Listing::with(['user.profile', 'receiver.profile'])
                ->where('driver_id', Auth::user()->id)
                ->whereIn('status', ['reserved', 'picked_up'])
                ->get();
        @endphp

        @if($myActiveDeliveries->isEmpty())
            <p class="text-sm text-gray-500">ليس لديك أي شحنات قيد التوصيل حالياً. تصفح الطلبات المتاحة في الأسفل للبدء.</p>
        @else
            <div class="grid grid-cols-1 gap-6">
                @foreach($myActiveDeliveries as $delivery)
                    <div class="border border-indigo-100 rounded-lg p-5 bg-indigo-50/30 flex flex-col md:flex-row justify-between items-start md:items-center">
                        <div class="space-y-2">
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <h4 class="text-md font-bold text-gray-900">{{ $delivery->title }}</h4>
                                @if($delivery->status === 'reserved')
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800">تم القبول - توجه للاستلام</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-800">قيد التوصيل للجمعية</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500">الكمية: {{ $delivery->quantity }} | تصنيف: {{ $delivery->category }}</p>
                            
                            <!-- مسار التوصيل المأمن بالـ Null-Safe Operator -->
                            <div class="text-xs text-gray-700 space-y-1">
                                <p>
                                    <strong class="text-green-700">1. من (المتبرع):</strong> 
                                    {{ $delivery->user?->profile?->organization_name ?? $delivery->user?->name ?? 'متبرع غير معروف' }} - {{ $delivery->address }}
                                </p>
                                <p>
                                    <strong class="text-indigo-700">2. إلى (الجمعية):</strong> 
                                    {{ $delivery->receiver?->profile?->organization_name ?? $delivery->receiver?->name ?? 'جمعية غير محددة' }} - {{ $delivery->receiver?->profile?->address ?? 'عنوان الجمعية غير متوفر' }}
                                </p>
                            </div>
                        </div>

                        <!-- أزرار تغيير الحالة اللوجستية للمندوب -->
                        <div class="mt-4 md:mt-0">
                            @if($delivery->status === 'reserved')
                                <!-- خطوة 1: تأكيد الاستلام من الفندق -->
                                <form method="POST" action="{{ route('listings.pickup-delivery', $delivery->id) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700">
                                        تأكيد استلام الشحنة من المتبرع
                                    </button>
                                </form>
                            @elseif($delivery->status === 'picked_up')
                                <!-- خطوة 2: تأكيد التسليم للجمعية واكتمال الطلب -->
                                <form method="POST" action="{{ route('listings.complete-delivery', $delivery->id) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                                        تأكيد تسليم الشحنة للجمعية (اكتمل)
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- 2. طلبات التوصيل المتاحة بالمنصة (المحجوزة من جمعيات وبانتظار مندوب) -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100 p-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">طلبات توصيل متاحة حالياً</h3>
        
        @php
            // جلب الطلبات المحجوزة من جمعية ولكن لم يقبلها أي مندوب بعد
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
                    <div class="border border-gray-200 rounded-lg p-5 bg-gray-50 flex flex-col justify-between">
                        <div>
                            <h4 class="text-md font-bold text-gray-900">{{ $delivery->title }}</h4>
                            <p class="text-xs text-gray-500 mt-1">الكمية: {{ $delivery->quantity }} | صالح حتى: {{ $delivery->expiry_time->format('Y-m-d H:i') }}</p>
                            
                            <!-- جهات الاستلام والتسليم المأمنة بالكامل -->
                            <div class="mt-4 space-y-1 text-xs text-gray-600">
                                <p>
                                    <strong>نقطة الاستلام:</strong> 
                                    {{ $delivery->user?->profile?->organization_name ?? $delivery->user?->name ?? 'متبرع غير معروف' }} ({{ $delivery->address }})
                                </p>
                                <p>
                                    <strong>نقطة التسليم:</strong> 
                                    {{ $delivery->receiver?->profile?->organization_name ?? $delivery->receiver?->name ?? 'جمعية غير محددة' }} ({{ $delivery->receiver?->profile?->address ?? 'مقر الجمعية' }})
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-t border-gray-200 flex justify-end">
                            @if(Auth::user()->status === 'active')
                                <form method="POST" action="{{ route('listings.accept-delivery', $delivery->id) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                        قبول مهمة التوصيل
                                    </button>
                                </form>
                            @else
                                <button disabled class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-300 cursor-not-allowed">
                                    قبول مهمة التوصيل (الحساب غير موثق)
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

<!-- 3. سجل عمليات التوصيل المنجزة (الأرشيف للمندوب) -->
    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100 p-6 mt-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">سجل العمليات التي قمت بتوصيلها</h3>
        
        @php
            // جلب عمليات التوصيل التي أنجزها هذا المندوب واكتملت
            $myCompletedDeliveries = \App\Models\Listing::where('driver_id', Auth::user()->id)
                ->where('status', 'completed')
                ->orderBy('updated_at', 'desc')
                ->get();
        @endphp

        @if($myCompletedDeliveries->isEmpty())
            <p class="text-sm text-gray-500">لا توجد عمليات توصيل منجزة ومسجلة باسمك بعد.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-right">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500">اسم الوجبة</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500">من (المتبرع)</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500">إلى (الجمعية)</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500">الكمية</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500">وقت التوصيل الفعلي</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($myCompletedDeliveries as $delivery)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $delivery->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $delivery->user?->profile?->organization_name ?? $delivery->user?->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $delivery->receiver?->profile?->organization_name ?? $delivery->receiver?->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $delivery->quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $delivery->updated_at->format('Y-m-d H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">مكتمل ومسلّم</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>