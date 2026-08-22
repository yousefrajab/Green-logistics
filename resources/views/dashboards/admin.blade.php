<div class="space-y-6" dir="rtl" x-data="{ activeTab: 'all' }">

    <!-- عرض إشعارات الإدارة -->
    @if(session('success'))
        <div class="bg-emerald-50 border-r-4 border-emerald-500 p-4 rounded-xl shadow-sm">
            <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('warning'))
        <div class="bg-yellow-50 border-r-4 border-yellow-500 p-4 rounded-xl shadow-sm">
            <p class="text-sm font-semibold text-yellow-800">{{ session('warning') }}</p>
        </div>
    @endif

    <!-- كروت الإحصائيات الرباعية الفخمة لمتابعة المنصة بالكامل -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- كرت الحسابات المعلقة -->
        <div class="bg-white overflow-hidden shadow-sm hover:shadow-md transition-all rounded-2xl border border-slate-100 p-6 flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-slate-400">حسابات بانتظار التوثيق</p>
                <p class="text-3xl font-extrabold text-amber-600">
                    {{ \App\Models\User::where('status', 'pending_verification')->whereIn('role', ['donor', 'receiver'])->count() }}
                </p>
            </div>
            <div class="p-3.5 rounded-2xl bg-amber-50 text-amber-600 border border-amber-100/50">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
        </div>

        <!-- كرت المتبرعين النشطين -->
        <div class="bg-white overflow-hidden shadow-sm hover:shadow-md transition-all rounded-2xl border border-slate-100 p-6 flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-slate-400">الشركاء والمتبرعون</p>
                <p class="text-3xl font-extrabold text-emerald-600">
                    {{ \App\Models\User::where('role', 'donor')->where('status', 'active')->count() }}
                </p>
            </div>
            <div class="p-3.5 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100/50">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16" />
                </svg>
            </div>
        </div>

        <!-- كرت الجمعيات النشطة -->
        <div class="bg-white overflow-hidden shadow-sm hover:shadow-md transition-all rounded-2xl border border-slate-100 p-6 flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-slate-400">الجمعيات الموثقة</p>
                <p class="text-3xl font-extrabold text-blue-600">
                    {{ \App\Models\User::where('role', 'receiver')->where('status', 'active')->count() }}
                </p>
            </div>
            <div class="p-3.5 rounded-2xl bg-blue-50 text-blue-600 border border-blue-100/50">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a4 4 0 00-4-4h-1" />
                </svg>
            </div>
        </div>

        <!-- كرت المناديب النشطين (الجديد والمكتمل) -->
        <div class="bg-white overflow-hidden shadow-sm hover:shadow-md transition-all rounded-2xl border border-slate-100 p-6 flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs font-semibold text-slate-400">مناديب التوصيل النشطين</p>
                <p class="text-3xl font-extrabold text-indigo-600">
                    {{ \App\Models\User::where('role', 'driver')->where('status', 'active')->count() }}
                </p>
            </div>
            <div class="p-3.5 rounded-2xl bg-indigo-50 text-indigo-600 border border-indigo-100/50">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1" />
                </svg>
            </div>
        </div>
    </div>

    <!-- 1. جدول إدارة الحسابات الجديدة المعلقة (بانتظار المراجعة والتوثيق) -->
    <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6">
        <h3 class="text-lg font-bold text-slate-900 mb-2">طلبات توثيق وتفعيل الحسابات الجديدة</h3>
        <p class="text-xs text-slate-400 mb-6">قائمة بالمؤسسات والجمعيات التي قامت بالتسجيل مؤخراً وبانتظار مراجعة الإدارة للوثائق الرسمية.</p>

        @php
            $pendingUsers = \App\Models\User::with('profile')
                ->where('status', 'pending_verification')
                ->whereIn('role', ['donor', 'receiver', 'driver']) // تشمل المندوبين الآن!
                ->orderBy('created_at', 'desc')
                ->get();
        @endphp

        @if($pendingUsers->isEmpty())
            <div class="text-center py-8 border border-dashed border-slate-200 rounded-2xl">
                <p class="text-sm text-gray-500">لا توجد طلبات توثيق معلقة حالياً. جميع الحسابات نشطة ومفعلة.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-right">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500">الاسم المسؤول</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500">الجهة / المؤسسة</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500">نوع الحساب</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500">بيانات التراخيص والتواصل</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500">الإجراءات والتوثيق</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @foreach($pendingUsers as $pUser)
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-slate-800">{{ $pUser->name }}</div>
                                    <div class="text-xs text-slate-400 mt-0.5">{{ $pUser->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-700">
                                    {{ $pUser->profile?->organization_name ?? 'غير محدد' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-bold">
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-bold">
                                    @if($pUser->role === 'donor') <span class="text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg">جهة متبرعة</span>
                                    @elseif($pUser->role === 'receiver') <span class="text-blue-750 bg-blue-50 px-2.5 py-1 rounded-lg">جمعية مستلمة</span>
                                    @elseif($pUser->role === 'driver') <span class="text-indigo-750 bg-indigo-50 px-2.5 py-1 rounded-lg">مندوب توصيل</span> <!-- أضفنا شارة المندوب هنا -->
                                    @endif
                                </td>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-500 space-y-1 font-semibold">
                                    <p>📞 الهاتف: {{ $pUser->profile?->phone ?? 'لا يوجد' }}</p>
                                    @if($pUser->role === 'receiver')
                                        <p class="text-indigo-600">📄 رخصة الجمعية: {{ $pUser->profile?->license_number ?? 'غير متوفر' }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-bold flex items-center space-x-3 space-x-reverse mt-2">
                                    <form method="POST" action="{{ route('admin.users.approve', $pUser->id) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg transition-colors">✓ موافقة وتفعيل</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.reject', $pUser->id) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg transition-colors">✕ تعليق</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- =========================================================
         2. لوحة إدارة وحوكمة جميع المشتركين بالمنصة (دليل المستخدمين الشامل)
    ========================================================== -->
    <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-6">
        <div class="sm:flex sm:items-center sm:justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-slate-900">سجل وحوكمة جميع المشتركين بالمنصة</h3>
                <p class="mt-1 text-xs text-slate-400">دليل شامل لكافة الأعضاء المسجلين مع إمكانية تحديث وتعديل حالتهم الأمنية والتشغيلية فوراً.</p>
            </div>
        </div>

        <!-- مبدل الفلاتر التفاعلي بـ Alpine.js لتسهيل التصفح كالمواقع الكبرى -->
        <div class="flex space-x-2 space-x-reverse mb-6 border-b border-slate-100 pb-4">
            <button @click="activeTab = 'all'" :class="activeTab === 'all' ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-1.5 text-xs font-bold rounded-xl transition-all">الكل</button>
            <button @click="activeTab = 'donors'" :class="activeTab === 'donors' ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-1.5 text-xs font-bold rounded-xl transition-all">الجهات المتبرعة</button>
            <button @click="activeTab = 'receivers'" :class="activeTab === 'receivers' ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-1.5 text-xs font-bold rounded-xl transition-all">الجمعيات الموثقة</button>
            <button @click="activeTab = 'drivers'" :class="activeTab === 'drivers' ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-1.5 text-xs font-bold rounded-xl transition-all">مناديب التوصيل</button>
        </div>

        @php
            // جلب جميع المستخدمين في النظام ما عدا المدير نفسه
            $allUsers = \App\Models\User::with('profile')
                ->where('id', '!=', Auth::user()->id)
                ->orderBy('name', 'asc')
                ->get();
        @endphp

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-right">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500">الاسم والبريد</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500">اسم الجهة</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500">نوع الحساب</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500">الحالة الحالية</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500">مغير الحالة الفوري (SaaS Custom)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @foreach($allUsers as $u)
                        <!-- نفلتر الأسطر برمجياً عبر Alpine.js بناءً على التبويب المختار بسلاسة فائقة -->
                        <tr x-show="activeTab === 'all' || 
                                   (activeTab === 'donors' && '{{ $u->role }}' === 'donor') || 
                                   (activeTab === 'receivers' && '{{ $u->role }}' === 'receiver') || 
                                   (activeTab === 'drivers' && '{{ $u->role }}' === 'driver')" 
                            x-transition
                            class="hover:bg-slate-50/30 transition-colors"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-slate-800">{{ $u->name }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ $u->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-600">
                                {{ $u->profile?->organization_name ?? 'غير محدد (حساب أفراد)' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs font-bold">
                                @if($u->role === 'donor') <span class="text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg">جهة متبرعة</span>
                                @elseif($u->role === 'receiver') <span class="text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg">جمعية مستلمة</span>
                                @elseif($u->role === 'driver') <span class="text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-lg">مندوب توصيل</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($u->status === 'active')
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-green-50 text-green-700 border border-green-200">نشط وموثق</span>
                                @elseif($u->status === 'pending_verification')
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-yellow-50 text-yellow-700 border border-yellow-200">بانتظار التفعيل</span>
                                @elseif($u->status === 'suspended')
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-red-50 text-red-700 border border-red-200">موقوف معلق</span>
                                @endif
                            </td>
                            
                            <!-- عمود مغير الحالة اللحظي التفاعلي والمثالي للتسويق والمنافسة -->
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                <form method="POST" action="{{ route('admin.users.update-status', $u->id) }}" class="flex items-center">
                                    @csrf
                                    <!-- بمجرد تغيير الخيار، يتم إرسال النموذج تلقائياً وحفظ الحالة في قاعدة البيانات فورياً! -->
                                    <select name="status" onchange="this.form.submit()" class="text-xs font-bold border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 py-1.5 px-3 bg-slate-50 cursor-pointer">
                                        <option value="active" {{ $u->status === 'active' ? 'selected' : '' }}>تفعيل وتوثيق (Active)</option>
                                        <option value="pending_verification" {{ $u->status === 'pending_verification' ? 'selected' : '' }}>تعليق بانتظار المراجعة (Pending)</option>
                                        <option value="suspended" {{ $u->status === 'suspended' ? 'selected' : '' }}>إيقاف وتجميد الحساب (Suspended)</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
