<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'حفظ النعمة') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=cairo:300,400,500,600,700,800" rel="stylesheet" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Cairo', sans-serif;
                background-color: #f8fafc; /* خلفية ناعمة جداً ومريحة للعين */
            }
            .sidebar-bg {
                background: linear-gradient(180deg, #041d2d 0%, #031420 100%);
                border-left: 1px solid rgba(255,255,255,0.06);
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="min-h-screen flex flex-col md:flex-row">

            <!-- =========================================================
                 RIGHT SIDEBAR - القائمة الجانبية الفاخرة والمطابقة للصورة 11
            ========================================================== -->
            <aside class="w-full md:w-64 sidebar-bg text-slate-300 flex flex-col justify-between p-6 shrink-0 relative z-30">
                <div class="space-y-8">
                    <!-- الشعار والهوية البصرية الراقية في أعلى القائمة الجانبية -->
                    <div class="flex items-center space-x-3 space-x-reverse pb-6 border-b border-white/5">
                        <div class="p-2 bg-emerald-500/10 rounded-xl text-emerald-400 border border-emerald-500/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-md font-extrabold text-white leading-none">نعمة سلسلة</h2>
                            <p class="text-[10px] text-emerald-400 font-bold mt-1">حفظ النعمة اللوجستية</p>
                        </div>
                    </div>

                    <!-- روابط التنقل السريعة والمصممة بأيقونات مخصصة لكل دور -->
                    <nav class="space-y-1">
                        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-xs font-bold rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-950/20' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                            <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                            </svg>
                            لوحة التحكم الرئيسية
                        </a>

                        @if(Auth::user()->role === 'donor' && Auth::user()->status === 'active')
                            <a href="{{ route('listings.create') }}" class="flex items-center px-4 py-3 text-xs font-bold rounded-xl transition-all text-slate-400 hover:bg-white/5 hover:text-white">
                                <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                إضافة إعلان فائض
                            </a>
                        @endif

                        <!-- مسارات المساعدة والدعم الفني للمستخدمين -->
                        <a href="#" class="flex items-center px-4 py-3 text-xs font-bold rounded-xl text-slate-400 hover:bg-white/5 hover:text-white transition-all">
                            <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            مركز المساعدة والدعم
                        </a>
                    </nav>
                </div>

                <!-- ملف المستخدم وتسجيل الخروج في أسفل القائمة الجانبية -->
                <div class="pt-6 border-t border-white/5 flex flex-col space-y-4">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold text-sm border border-emerald-500/20">
                            {{ mb_substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white leading-none">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-slate-400 mt-1 truncate max-w-[140px]">{{ Auth::user()->email }}</p>
                        </div>
                    </div>

                    <!-- رابط تسجيل الخروج السهل والآمن -->
                    <a href="/logout" class="flex items-center px-4 py-2.5 text-xs font-bold text-rose-400 hover:bg-rose-500/10 rounded-xl transition-all">
                        <svg class="w-4 h-4 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        تسجيل الخروج
                    </a>
                </div>
            </aside>

            <!-- =========================================================
                 LEFT CONTENT AREA - ترويسة علوية أنيقة ومساحة العمل الرئيسية
            ========================================================== -->
            <div class="flex-grow flex flex-col min-h-screen overflow-hidden">
                
                <!-- الترويسة العلوية الفخمة (Top Header) -->
                <header class="bg-white border-b border-slate-100 px-8 py-4 flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0 z-20">
                    <div>
                        @if (isset($header))
                            {{ $header }}
                        @else
                            <h2 class="font-extrabold text-xl text-slate-900 leading-tight">سلسلة حفظ النعمة اللوجستية</h2>
                        @endif
                    </div>

                    <!-- أزرار الإجراءات وحالة الحساب الشخصي للمستخدم -->
                    <div class="flex items-center space-x-4 space-x-reverse">
                        <span class="px-3 py-1.5 text-xs font-bold rounded-full 
                            @if(Auth::user()->status === 'active') bg-emerald-50 text-emerald-700 border border-emerald-100
                            @else bg-yellow-50 text-yellow-700 border border-yellow-100 @endif animate-pulse">
                            {{ Auth::user()->status === 'active' ? '● حساب موثق ونشط' : '● بانتظار توثيق الإدارة' }}
                        </span>

                        <div class="h-6 w-px bg-slate-200"></div>

                        <!-- إشعار بسيط باللغة -->
                        <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">عربي (RTL)</span>
                    </div>
                </header>

                <!-- مساحة المحتوى واللوحات الرئيسية للمتبرعين والجمعيات والمندوبين -->
                <main class="flex-grow p-6 md:p-8 overflow-y-auto">
                    {{ $slot }}
                </main>

            </div>

        </div>
    </body>
</html>