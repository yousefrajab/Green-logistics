<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شهادة شريك الاستدامة الموثقة - جود</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cairo:400,600,700,800" rel="stylesheet" />
    
    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8fafc;
        }
        /* إطار فخم ومزدوج لشهادة التقدير الرسمية */
        .certificate-border {
            border: 12px double #10b981; /* إطار زمردي مزدوج فخم */
            padding: 2.5rem;
            background-color: #ffffff;
        }
        @media print {
            body {
                background-color: #ffffff;
            }
            .no-print {
                display: none !important;
            }
            .print-area {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
<body class="p-6 md:p-12">

    <div class="max-w-4xl mx-auto space-y-6 print-area">
        
        <!-- أزرار الإجراءات التفاعلية (تختفي عند حفظ الـ PDF) -->
        <div class="no-print flex justify-between items-center bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
            <a href="{{ route('dashboard') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors">
                ← العودة للوحة التحكم
            </a>
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-transparent text-xs font-bold rounded-xl shadow-md shadow-emerald-100 text-white bg-emerald-600 hover:bg-emerald-700 transition-all">
                🖨️ طباعة أو حفظ الشهادة بصيغة (PDF)
            </button>
        </div>

        <!-- كرت شهادة التقدير الفاخر والمطابق للتصميم والمحاذاة الفنية -->
        <div class="bg-white border border-slate-200 rounded-[2.5rem] p-4 shadow-xl relative overflow-hidden">
            <div class="certificate-border rounded-[2rem] relative z-10 text-center space-y-8">
                
                <!-- الخلفية الفنية الخفيفة للشهادة -->
                <div class="absolute inset-0 opacity-[0.03] bg-[radial-gradient(#000_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>

                <!-- الشعار والمستوى الفخم في الأعلى -->
                <div class="flex flex-col items-center space-y-3">
                    <div class="p-3 bg-emerald-50 rounded-2xl text-emerald-600 border border-emerald-100 flex items-center justify-center shadow-sm">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04" />
                        </svg>
                    </div>
                    <span class="text-xs font-black text-emerald-600 tracking-widest uppercase">الشبكة اللوجستية الوطنية لحفظ النعمة</span>
                </div>

                <!-- عنوان الشهادة الرسمي الفخم -->
                <div class="space-y-2">
                    <h1 class="text-3xl font-black text-slate-900 leading-tight">شهادة شكر وتقدير شريك الاستدامة</h1>
                    <p class="text-xs text-slate-400">تُمنح هذه الشهادة الموثقة تقديراً للجهود الاستثنائية والمسؤولة تجاه المجتمع والبيئة</p>
                </div>

                <!-- اسم المنشأة والمسؤول ببنط عريض وجذاب كأكبر الشهادات العالمية -->
                <div class="py-6 border-t border-b border-slate-100 max-w-2xl mx-auto space-y-4">
                    <p class="text-sm font-semibold text-slate-500">تتشرف إدارة منصة جود بتقدير الشريك الاستراتيجي:</p>
                    <h2 class="text-3xl font-black text-emerald-600 tracking-wide">{{ $organizationName }}</h2>
                    <p class="text-xs font-medium text-slate-400">نظير مساهمتكم الكريمة والمستمرة في حماية الأمن الغذائي وإرساء معايير الاقتصاد الدائري.</p>
                </div>

                <!-- الأثر البيئي الحقيقي والدقيق من قاعدة البيانات المعروض بشكل بكتل ملونة أنيقة -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-xl mx-auto pt-4">
                    <div class="bg-emerald-50/50 border border-emerald-100/50 p-4 rounded-2xl text-center">
                        <p class="text-[10px] text-emerald-800 font-bold">📦 الوجبات التي تم إنقاذها</p>
                        <p class="text-xl font-extrabold text-emerald-600 mt-1">{{ $completedCount }} وجبة حقيقية</p>
                    </div>
                    <div class="bg-blue-50/50 border border-blue-100/50 p-4 rounded-2xl text-center">
                        <p class="text-[10px] text-blue-800 font-bold">💨 غاز الـ (CO2) المحفوظ</p>
                        <p class="text-xl font-extrabold text-blue-600 mt-1">{{ $report['co2_saved'] }}</p>
                    </div>
                    <div class="bg-cyan-50/50 border border-cyan-100/50 p-4 rounded-2xl text-center">
                        <p class="text-[10px] text-cyan-800 font-bold">💧 مياه عذبة تم توفيرها</p>
                        <p class="text-xl font-extrabold text-cyan-600 mt-1">{{ $report['water_saved'] }}</p>
                    </div>
                </div>

                <!-- التقرير النصي البليغ والمختصر جداً المولد بالذكاء الاصطناعي [4] -->
                <div class="max-w-2xl mx-auto bg-slate-50 border border-slate-100 p-6 rounded-2xl">
                    <p class="text-xs text-slate-700 leading-relaxed font-semibold text-justify whitespace-pre-line leading-relaxed">
                        {{ $report['report_text'] }}
                    </p>
                </div>

                <!-- التواقيع الرسمية للشهادة لتبدو كوثيقة جاهزة للتأطير والتعليق في مكاتب الفنادق -->
                <div class="grid grid-cols-2 gap-12 pt-10 text-xs text-slate-500 font-bold max-w-md mx-auto">
                    <div>
                        <p class="border-b border-slate-200 pb-12"></p>
                        <p class="mt-2 text-slate-800 font-extrabold">إدارة منصة جود</p>
                    </div>
                    <div>
                        <p class="border-b border-slate-200 pb-12"></p>
                        <p class="mt-2 text-slate-800 font-extrabold">مفتش الرقابة وسلامة الأغذية</p>
                    </div>
                </div>

                <!-- رمز التوثيق الإلكتروني في الأسفل -->
                <div class="text-[9px] text-slate-400 pt-8 border-t border-slate-100 flex justify-between items-center">
                    <span>رمز التوثيق المعتمد: #JO-CSR-{{ $completedCount }}-2026</span>
                    <span>منصة جود لحفظ النعمة © 2026</span>
                </div>

            </div>
        </div>

    </div>

    <!-- كود جافا سكريبت اللطيف لفتح شاشة طباعة المتصفح وحفظ الـ PDF تلقائياً بمجرد تحميل الصفحة -->
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            // نمنح المتصفح مهلة نصف ثانية لتحميل التدرجات والألوان بالكامل ثم نفتح نافذة الحفظ/الطباعة تلقائياً
            setTimeout(function() {
                window.print();
            }, 600);
        });
    </script>

</body>
</html>