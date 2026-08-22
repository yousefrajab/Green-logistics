<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'الجود') }}</title>

    <!-- Cairo Font -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cairo:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            font-family: 'Cairo', sans-serif;
        }

        body {
            margin: 0;
            overflow-x: hidden;
        }

        .login-page {
            min-height: 100vh;
        }

        /* خلفية الخريطة */
        .map-background {
            background:
                radial-gradient(circle at 30% 25%, rgba(16, 185, 129, .18), transparent 25%),
                radial-gradient(circle at 75% 65%, rgba(6, 182, 212, .14), transparent 25%),
                linear-gradient(135deg, #031b2b 0%, #05283a 48%, #02141f 100%);
        }

        .map-grid {
            background-image:
                linear-gradient(rgba(255,255,255,.045) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.045) 1px, transparent 1px);
            background-size: 38px 38px;
        }

        .glass-card {
            background: rgba(4, 29, 45, .82);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255,255,255,.18);
            box-shadow: 0 25px 70px rgba(0,0,0,.35);
        }

        .route-line {
            stroke-dasharray: 10 8;
            animation: routeMove 8s linear infinite;
        }

        @keyframes routeMove {
            to {
                stroke-dashoffset: -180;
            }
        }

        .location-pulse {
            animation: pulseLocation 2s infinite;
        }

        @keyframes pulseLocation {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.12);
                opacity: .75;
            }
        }

        .truck-float {
            animation: truckFloat 4s ease-in-out infinite;
        }

        @keyframes truckFloat {
            0%, 100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        .input-modern {
            transition: all .2s ease;
        }

        .input-modern:focus {
            box-shadow: 0 0 0 4px rgba(16, 185, 129, .10);
        }

        /* =========================================================
             كود CSS المطور والمشحون يدوياً لتشغيل الـ Dark Mode الفاخر فوراً
        ========================================================== */
        .dark, .dark main, .dark .bg-white {
            background-color: #0b1523 !important; /* خلفية كحلية داكنة فخمة للموقع */
            color: #f1f5f9 !important;
        }
        .dark .text-slate-900, .dark h2 {
            color: #ffffff !important;
        }
        .dark .text-slate-500, .dark p {
            color: #94a3b8 !important;
        }
        .dark .border-slate-200, .dark .border-slate-100 {
            border-color: #1e293b !important;
        }
        .dark input, .dark select {
            background-color: #111e2f !important;
            color: #ffffff !important;
            border-color: #243547 !important;
        }
        .dark .bg-slate-50 {
            background-color: #08101c !important;
        }
        .dark .text-slate-600, .dark span {
            color: #cbd5e1 !important;
        }
        .dark select option {
            background-color: #111e2f !important;
            color: #ffffff !important;
        }
        .dark .bg-white select {
            background-color: #111e2f !important;
            color: #ffffff !important;
        }
        .dark button[type="button"]:hover, .dark button[type="button"].bg-white/5 {
            background-color: rgba(255, 255, 255, 0.05) !important;
        }
    </style>
</head>

<!-- نفعّل كاش المتصفح لحفظ اختيار العميل للوضع المفضل (فاتح/داكن) عبر تتبع المتغير بـ Alpine -->
<body 
    x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
    x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
    :class="{ 'dark': darkMode }"
    class="bg-gray-50 text-gray-900 transition-colors duration-300"
>

<div class="login-page flex min-h-screen flex-col md:flex-row" dir="ltr">

    <!-- LEFT SIDE - LOGISTICS MAP -->
    <section class="relative hidden min-h-screen overflow-hidden md:flex md:w-1/2 map-background text-white" dir="rtl">
        <div class="map-grid absolute inset-0 opacity-40"></div>
        <div class="absolute -top-40 -left-40 h-96 w-96 rounded-full bg-emerald-500/10 blur-3xl"></div>
        <div class="absolute bottom-0 right-0 h-96 w-96 rounded-full bg-cyan-500/10 blur-3xl"></div>

        <svg class="absolute inset-0 h-full w-full opacity-80" viewBox="0 0 900 1000" preserveAspectRatio="none">
            <g stroke="#294b5d" stroke-width="2" fill="none">
                <path d="M-50 130 C160 80 260 260 430 170 S700 90 950 150"/>
                <path d="M-40 260 C160 180 260 400 470 310 S730 240 950 330"/>
                <path d="M-50 410 C180 350 270 520 430 430 S700 390 950 470"/>
                <path d="M-80 580 C160 500 260 680 450 570 S720 540 970 620"/>
                <path d="M-50 750 C180 650 300 820 500 720 S750 700 950 770"/>
                <path d="M20 900 C210 780 340 950 520 850 S780 820 940 900"/>

                <path d="M120 -30 C80 190 300 230 200 470 S230 730 130 1030"/>
                <path d="M300 -30 C360 180 260 330 380 500 S350 770 430 1030"/>
                <path d="M510 -30 C450 180 600 280 500 460 S600 720 550 1030"/>
                <path d="M700 -30 C630 190 780 290 690 490 S800 720 720 1030"/>
                <path d="M850 -30 C780 190 910 350 800 520 S900 750 850 1030"/>
            </g>

            <g fill="none" stroke="#34d399" stroke-width="5" stroke-linecap="round">
                <path class="route-line" d="M40 690 C180 580 220 350 410 390 C560 420 560 180 850 130"/>
                <path class="route-line" stroke="#22d3ee" d="M80 230 C250 170 270 300 400 500 C520 680 700 700 870 850"/>
                <path class="route-line" stroke="#86efac" d="M140 850 C250 700 420 740 500 580 C590 400 720 470 820 300"/>
            </g>

            <g>
                <circle cx="410" cy="390" r="13" fill="#10b981"/>
                <circle cx="410" cy="390" r="28" fill="#10b981" opacity=".18"/>
                <circle cx="560" cy="180" r="13" fill="#22d3ee"/>
                <circle cx="560" cy="180" r="28" fill="#22d3ee" opacity=".18"/>
                <circle cx="700" cy="700" r="13" fill="#10b981"/>
                <circle cx="700" cy="700" r="28" fill="#10b981" opacity=".18"/>
                <circle cx="220" cy="350" r="13" fill="#60a5fa"/>
                <circle cx="220" cy="350" r="28" fill="#60a5fa" opacity=".18"/>
            </g>
        </svg>

        <!-- TOP BRAND -->
        <div class="relative z-20 flex w-full flex-col justify-between p-8 xl:p-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500/15 border border-emerald-400/20">
                        <svg class="h-7 w-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 7a5 5 0 100 10 5 5 0 000-10z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-extrabold">الجود</h1>
                        <p class="text-xs text-emerald-300/70">منصة الجود اللوجستية لحفظ النعمة</p>
                    </div>
                </div>

                <div class="flex items-center rounded-full border border-white/10 bg-white/5 p-1 text-xs">
                    <button class="rounded-full px-3 py-1.5 text-white/70">English</button>
                    <button class="rounded-full bg-emerald-500/20 px-3 py-1.5 font-bold text-emerald-300">عربي</button>
                </div>
            </div>

            <!-- CENTER MESSAGE -->
            <div class="relative z-20 my-auto max-w-xl py-16">
                <span class="inline-flex items-center rounded-full border border-emerald-400/20 bg-emerald-500/10 px-4 py-1.5 text-xs font-semibold text-emerald-300">مشروع وطني مستدام</span>
                <h2 class="mt-5 text-4xl font-extrabold leading-[1.35] xl:text-5xl">معاً نحول الفائض <span class="text-emerald-400">إلى قيمة.</span></h2>
                <p class="mt-5 max-w-lg text-sm leading-8 text-slate-300">
                    منصة الجود الذكية تربط الجهات المتبرعة بالجمعيات والمندوبين لتقليل الهدر، وإنقاذ الطعام الفائض، وإيصاله إلى مستحقيه بأمان وسرعة.
                </p>
            </div>

            <!-- IMPACT CARD -->
            <!-- ================= IMPACT CARD (الآن ديناميكي وحقيقي 100%) ================= -->
            <div class="glass-card relative z-20 rounded-3xl p-6">

                <div class="mb-5 text-center">
                    <h3 class="text-lg font-bold">
                        تأثيرنا اليوم
                    </h3>

                    <p class="mt-1 text-xs text-slate-400">
                        معاً نصنع أثراً مستداماً وحقيقياً
                    </p>
                </div>

                <div class="grid grid-cols-3 divide-x divide-x-reverse divide-white/10">

                    <!-- وجبات تم إنقاذها (تحديث المسمى ليكون متوافقاً مع حساب الوجبات الحقيقي) -->
                    <div class="text-center">
                        <div class="mx-auto mb-2 flex h-11 w-11 items-center justify-center rounded-full bg-emerald-500/15">
                            <svg class="h-6 w-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10V4a1 1 0 00-1-1h-1l-4 5v11h7.28a2 2 0 001.94-1.515l1.5-6A2 2 0 0016.78 10H14z" />
                            </svg>
                        </div>

                        <p class="text-2xl font-extrabold text-emerald-400">
                            +{{ $completedCount }}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            وجبة تم إنقاذها
                        </p>
                    </div>

                    <!-- شاحنة/مندوب يعمل الآن -->
                    <div class="text-center">
                        <div class="mx-auto mb-2 flex h-11 w-11 items-center justify-center rounded-full bg-cyan-500/15">
                            <svg class="h-6 w-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m-9 4h10m-9 4h8M5 21h14a2 2 0 002-2V7l-4-4H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>

                        <p class="text-2xl font-extrabold text-cyan-400">
                            +{{ $activeDrivers }}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            مندوب نشط معنا
                        </p>
                    </div>

                    <!-- جمعية شريكة نشطة -->
                    <div class="text-center">
                        <div class="mx-auto mb-2 flex h-11 w-11 items-center justify-center rounded-full bg-emerald-500/15">
                            <svg class="h-6 w-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m7-7a4 4 0 11-8 0 4 4 0 018 0zm5 1a3 3 0 11-6 0" />
                            </svg>
                        </div>

                        <p class="text-2xl font-extrabold text-emerald-400">
                            +{{ $partnerCharities }}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            جمعية شريكة
                        </p>
                    </div>

                </div>

                <div class="mt-5 border-t border-white/10 pt-4 text-center">
                    <span class="text-xs text-emerald-300">
                        معاً، نحول الفائض إلى قيمة وأثر مستدام
                    </span>
                </div>

            </div>

            <!-- Bottom security -->
            <div class="relative z-20 mt-6 flex items-center justify-center gap-8 text-xs text-slate-400">
                <span class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.1.9-2 2-2s2 .9 2 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2a2 2 0 012-2z"/>
                    </svg>
                    بيانات مشفرة
                </span>
                <span class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 11-12.728 0"/>
                    </svg>
                    دعم مستمر
                </span>
                <span class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 3c-2.756 0-5.29.936-7.31 2.506"/>
                    </svg>
                    أمان عالي
                </span>
            </div>
        </div>
    </section>

    <!-- RIGHT SIDE - FORM -->
    <main class="flex min-h-screen w-full items-center justify-center bg-white px-6 py-10 sm:px-10 md:w-1/2 transition-colors duration-300" dir="rtl">
        <div class="w-full max-w-xl">
            <!-- Mobile Logo -->
            <div class="mb-10 flex items-center justify-center gap-3 md:hidden">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-lg shadow-emerald-200">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900">الجود</h1>
                    <p class="text-xs text-emerald-600">المنصة اللوجستية</p>
                </div>
            </div>

            {{ $slot }}
        </div>
    </main>
</div>

</body>
</html>