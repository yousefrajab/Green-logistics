<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'حفظ النعمة') }}</title>

    <!-- Cairo Font -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cairo:400,500,600,700,800,900" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            font-family: 'Cairo', sans-serif;
        }

        body {
            margin: 0;
            overflow-x: hidden;
        }

        .auth-page {
            min-height: 100vh;
        }

        /* خلفية الخريطة */
        .map-background {
            background:
                radial-gradient(circle at 25% 20%, rgba(16, 185, 129, .20), transparent 30%),
                radial-gradient(circle at 80% 70%, rgba(6, 182, 212, .16), transparent 32%),
                linear-gradient(160deg, #03192a 0%, #06283d 45%, #041722 100%);
        }

        .map-grid {
            background-image:
                linear-gradient(rgba(255,255,255,.045) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.045) 1px, transparent 1px);
            background-size: 40px 40px;
            mask-image: radial-gradient(circle at 50% 40%, black 0%, transparent 80%);
        }

        .glass-card {
            background: rgba(5, 32, 48, .78);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,.14);
            box-shadow: 0 30px 80px rgba(0,0,0,.4);
        }

        .route-line {
            stroke-dasharray: 10 8;
            animation: routeMove 8s linear infinite;
        }

        @keyframes routeMove {
            to { stroke-dashoffset: -180; }
        }

        .pulse-dot {
            animation: pulseDot 2.2s ease-in-out infinite;
            transform-origin: center;
        }

        @keyframes pulseDot {
            0%, 100% { transform: scale(1); opacity: .9; }
            50% { transform: scale(1.25); opacity: .35; }
        }

        .fade-up {
            animation: fadeUp .6s cubic-bezier(.16,1,.3,1) both;
        }

        .fade-up-delay-1 { animation-delay: .08s; }
        .fade-up-delay-2 { animation-delay: .16s; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .input-modern {
            transition: box-shadow .2s ease, border-color .2s ease;
        }

        .input-modern:focus {
            box-shadow: 0 0 0 4px rgba(16, 185, 129, .10);
        }

        .brand-mark {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900">

<div class="auth-page flex min-h-screen flex-col md:flex-row" dir="ltr">

    <!-- =========================================================
         LEFT SIDE - LOGISTICS MAP / BRAND
    ========================================================== -->
    <section class="relative hidden min-h-screen overflow-hidden md:flex md:w-1/2 map-background text-white" dir="rtl">

        <!-- Grid -->
        <div class="map-grid absolute inset-0 opacity-40"></div>

        <!-- Glow -->
        <div class="pointer-events-none absolute -top-40 -left-40 h-96 w-96 rounded-full bg-emerald-500/10 blur-3xl"></div>
        <div class="pointer-events-none absolute bottom-0 right-0 h-96 w-96 rounded-full bg-cyan-500/10 blur-3xl"></div>

        <!-- ================= MAP SVG ================= -->
        <svg class="absolute inset-0 h-full w-full opacity-80" viewBox="0 0 900 1000" preserveAspectRatio="none">
            <!-- طرق الخريطة -->
            <g stroke="#26485c" stroke-width="2" fill="none">
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

            <!-- مسارات التوزيع -->
            <g fill="none" stroke="#34d399" stroke-width="5" stroke-linecap="round">
                <path id="route-1" class="route-line" d="M40 690 C180 580 220 350 410 390 C560 420 560 180 850 130"/>
                <path id="route-2" class="route-line" stroke="#22d3ee" d="M80 230 C250 170 270 300 400 500 C520 680 700 700 870 850"/>
                <path id="route-3" class="route-line" stroke="#86efac" d="M140 850 C250 700 420 740 500 580 C590 400 720 470 820 300"/>
            </g>

            <!-- شاحنة متحركة على المسار -->
            <circle r="7" fill="#ecfdf5" stroke="#10b981" stroke-width="3">
                <animateMotion dur="7s" repeatCount="indefinite" rotate="auto"
                    path="M40 690 C180 580 220 350 410 390 C560 420 560 180 850 130" />
            </circle>

            <!-- نقاط التوزيع -->
            <g>
                <circle class="pulse-dot" cx="410" cy="390" r="13" fill="#10b981"/>
                <circle cx="410" cy="390" r="28" fill="#10b981" opacity=".16"/>

                <circle class="pulse-dot" cx="560" cy="180" r="13" fill="#22d3ee"/>
                <circle cx="560" cy="180" r="28" fill="#22d3ee" opacity=".16"/>

                <circle class="pulse-dot" cx="700" cy="700" r="13" fill="#10b981"/>
                <circle cx="700" cy="700" r="28" fill="#10b981" opacity=".16"/>

                <circle class="pulse-dot" cx="220" cy="350" r="13" fill="#60a5fa"/>
                <circle cx="220" cy="350" r="28" fill="#60a5fa" opacity=".16"/>
            </g>
        </svg>

        <!-- ================= TOP BRAND / CONTENT ================= -->
        <div class="relative z-20 flex w-full flex-col justify-between p-8 xl:p-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="brand-mark flex h-12 w-12 items-center justify-center rounded-xl shadow-lg shadow-emerald-900/40">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V9h14v12M8 9V5h8v4" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-extrabold">حفظ النعمة</h1>
                        <p class="text-xs text-emerald-300/70">سلسلة لوجستية مستدامة</p>
                    </div>
                </div>

                <!-- Language -->
                <div class="flex items-center rounded-full border border-white/10 bg-white/5 p-1 text-xs">
                    <button type="button" class="rounded-full px-3 py-1.5 text-white/70 transition hover:text-white">English</button>
                    <button type="button" class="rounded-full bg-emerald-500/20 px-3 py-1.5 font-bold text-emerald-300">عربي</button>
                </div>
            </div>

            <!-- ================= CENTER MESSAGE ================= -->
            <div class="relative z-20 my-auto max-w-xl py-16">
                <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-400/20 bg-emerald-500/10 px-4 py-1.5 text-xs font-semibold text-emerald-300">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    مشروع وطني مستدام
                </span>
                <h2 class="mt-5 text-4xl font-extrabold leading-[1.35] xl:text-5xl">معاً نحول الفائض <span class="text-emerald-400">إلى قيمة.</span></h2>
                <p class="mt-5 max-w-lg text-sm leading-8 text-slate-300">
                    منصة ذكية تربط الجهات المتبرعة بالجمعيات والمندوبين لتقليل الهدر، وإنقاذ الطعام الفائض، وإيصاله إلى مستحقيه بأمان وسرعة.
                </p>
            </div>

            <!-- ================= IMPACT CARD ================= -->
            <div class="glass-card relative z-20 rounded-3xl p-6">
                <div class="mb-5 text-center">
                    <h3 class="text-lg font-bold">تأثيرنا اليوم</h3>
                    <p class="mt-1 text-xs text-slate-400">معاً نصنع أثراً مستداماً</p>
                </div>

                <div class="grid grid-cols-3 divide-x divide-x-reverse divide-white/10">
                    <div class="text-center">
                        <div class="mx-auto mb-2 flex h-11 w-11 items-center justify-center rounded-full bg-emerald-500/15">
                            <svg class="h-6 w-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10V4a1 1 0 00-1-1h-1l-4 5v11h7.28a2 2 0 001.94-1.515l1.5-6A2 2 0 0016.78 10H14z" />
                            </svg>
                        </div>
                        <p class="text-2xl font-extrabold text-emerald-400">50,000+</p>
                        <p class="mt-1 text-xs text-slate-400">طن تم إنقاذها</p>
                    </div>

                    <div class="text-center">
                        <div class="mx-auto mb-2 flex h-11 w-11 items-center justify-center rounded-full bg-cyan-500/15">
                            <svg class="h-6 w-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8m-9 4h10m-9 4h8M5 21h14a2 2 0 002-2V7l-4-4H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-2xl font-extrabold text-cyan-400">300+</p>
                        <p class="mt-1 text-xs text-slate-400">شاحنة تعمل الآن</p>
                    </div>

                    <div class="text-center">
                        <div class="mx-auto mb-2 flex h-11 w-11 items-center justify-center rounded-full bg-emerald-500/15">
                            <svg class="h-6 w-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m7-7a4 4 0 11-8 0 4 4 0 018 0zm5 1a3 3 0 11-6 0" />
                            </svg>
                        </div>
                        <p class="text-2xl font-extrabold text-emerald-400">120+</p>
                        <p class="mt-1 text-xs text-slate-400">جمعية شريكة</p>
                    </div>
                </div>

                <div class="mt-5 border-t border-white/10 pt-4 text-center">
                    <span class="text-xs text-emerald-300">معاً، نحول الفائض إلى قيمة وأثر مستدام</span>
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

    <!-- =========================================================
         RIGHT SIDE - FORM
    ========================================================== -->
    <main class="flex min-h-screen w-full items-center justify-center bg-white px-6 py-10 sm:px-10 md:w-1/2" dir="rtl">
        <div class="fade-up w-full max-w-xl">
            <!-- Mobile Logo -->
            <div class="mb-10 flex items-center justify-center gap-3 md:hidden">
                <div class="brand-mark flex h-12 w-12 items-center justify-center rounded-xl text-white shadow-lg shadow-emerald-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V9h14v12M8 9V5h8v4" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-slate-900">حفظ النعمة</h1>
                    <p class="text-xs text-emerald-600">سلسلة لوجستية مستدامة</p>
                </div>
            </div>

            {{ $slot }}
        </div>
    </main>
</div>

</body>
</html>