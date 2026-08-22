<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إيصال استلام وحفظ النعمة - جود</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cairo:400,600,700,800" rel="stylesheet" />
    
    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f1f5f9;
        }
        /* ستايل مخصص للطباعة لضمان ظهور الإيصال كـ PDF رسمي بدون هوامش ميتة */
        @media print {
            body {
                background-color: #ffffff;
            }
            .no-print {
                display: none !important;
            }
            .print-card {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
<body class="p-6 md:p-12">

    <div class="max-w-3xl mx-auto space-y-6">
        
        <!-- زر العودة والطباعة التفاعلية (يختفي تلقائياً عند الطباعة أو الحفظ كـ PDF) -->
        <div class="no-print flex justify-between items-center bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
            <a href="{{ route('dashboard') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors">
                ← العودة للوحة التحكم
            </a>
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-transparent text-xs font-bold rounded-xl shadow-md shadow-emerald-100 text-white bg-emerald-600 hover:bg-emerald-700 transition-all">
                🖨️ طباعة أو تحميل الإيصال (PDF)
            </button>
        </div>

        <!-- كرت الوثيقة الرسمية (إيصال استلام وحفظ النعمة اللوجستي) -->
        <div class="print-card bg-white border border-slate-200/80 rounded-3xl p-8 md:p-10 shadow-lg relative overflow-hidden">
            <!-- خلفية فنية رقيقة للوثيقة الرسمية -->
            <div class="absolute inset-0 opacity-5 bg-[radial-gradient(#000_1px,transparent_1px)] [background-size:16px_16px]"></div>
            <div class="absolute -top-16 -left-16 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl"></div>

            <!-- هيدر الوثيقة الرسمي -->
            <div class="relative z-10 flex justify-between items-center border-b border-slate-100 pb-8">
                <div class="space-y-1">
                    <h1 class="text-xl font-extrabold text-slate-900 leading-none">منصة جود لحفظ النعمة</h1>
                    <p class="text-[10px] text-emerald-600 font-bold tracking-wide">الشبكة اللوجستية الدائرية للغذاء</p>
                </div>
                <!-- لوجو جود الفاخر -->
                <div class="p-2.5 bg-emerald-50 rounded-2xl text-emerald-600 border border-emerald-100 flex items-center justify-center">
                    <span class="text-lg font-black leading-none">جود</span>
                </div>
            </div>

            <!-- عنوان ورمز التوثيق الإلكتروني -->
            <div class="relative z-10 text-center py-6">
                <span class="px-3 py-1 text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 rounded-full">وثيقة رسمية موثقة ومعتمدة</span>
                <h2 class="text-lg font-bold text-slate-800 mt-3">إيصال توصيل واستلام وجبات فائضة</h2>
                <p class="text-xs text-slate-400 mt-1">رمز توثيق المعاملة: #JO-{{ $listing->id }}-{{ $listing->created_at->format('Y') }}</p>
            </div>

            <!-- شبكة تفاصيل المعاملة اللوجستية الشاملة -->
            <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-b border-slate-100 py-8 text-xs leading-relaxed text-slate-600">
                
                <!-- بيانات الجهة المتبرعة (المصدر) -->
                <div class="space-y-2 border-l border-slate-100/80 pl-4">
                    <h3 class="font-bold text-slate-900 text-sm pb-1 border-b border-slate-100">1. بيانات الجهة المتبرعة</h3>
                    <p><strong>اسم المنشأة:</strong> {{ $listing->user?->profile?->organization_name ?? 'غير محدد' }}</p>
                    <p><strong>اسم المسؤول:</strong> {{ $listing->user?->name }}</p>
                    <p><strong>رقم الهاتف:</strong> {{ $listing->user?->profile?->phone ?? 'غير متوفر' }}</p>
                    <p><strong>عنوان الاستلام:</strong> {{ $listing->address }}</p>
                </div>

                <!-- بيانات الجمعية المستلمة (الهدف) -->
                <div class="space-y-2 pr-2">
                    <h3 class="font-bold text-slate-900 text-sm pb-1 border-b border-slate-100">2. بيانات الجمعية المستلمة</h3>
                    <p><strong>اسم الجمعية:</strong> {{ $listing->receiver?->profile?->organization_name ?? 'غير محدد' }}</p>
                    <p><strong>اسم المستلم المسؤول:</strong> {{ $listing->receiver?->name }}</p>
                    <p><strong>رقم ترخيص الجمعية:</strong> {{ $listing->receiver?->profile?->license_number ?? 'غير متوفر' }}</p>
                    <p><strong>مقر التسليم:</strong> {{ $listing->receiver?->profile?->address ?? 'غير محدد' }}</p>
                </div>

                <!-- بيانات المندوب الناقل والتوقيت -->
                <div class="space-y-2 border-l border-slate-100/80 pl-4 md:col-span-2 pt-4 border-t border-slate-100">
                    <h3 class="font-bold text-slate-900 text-sm pb-1 border-b border-slate-100">3. تفاصيل الحركة والتسليم اللوجستي</h3>
                    <p><strong>المندوب الناقل:</strong> {{ $listing->driver?->name ?? 'تم الاستلام مباشرة من الجمعية' }} (هاتف: {{ $listing->driver?->profile?->phone ?? '-' }})</p>
                    <p><strong>تاريخ ووقت التسليم الفعلي:</strong> {{ $listing->updated_at->format('Y-m-d H:i') }}</p>
                    <p><strong>كمية ونوع الشحنة المستلمة:</strong> <span class="font-extrabold text-slate-900 bg-emerald-50 px-2 py-0.5 rounded-md text-xs">{{ $listing->quantity }} (تصنيف: {{ $listing->category }})</span></p>
                    <p><strong>تفاصيل ومكونات الأغذية:</strong> {{ $listing->description ?? 'تم مراجعة سلامة وتغليف الأغذية من قِبل الأطراف.' }}</p>
                </div>
            </div>

            <!-- التوقيعات والأختام الرسمية المعلقة في الأسفل -->
            <div class="relative z-10 grid grid-cols-3 gap-6 pt-12 text-center text-xs text-slate-500 font-semibold">
                <div>
                    <p class="border-b border-slate-200 pb-8"></p>
                    <p class="mt-2">توقيع مسؤول المتبرع</p>
                </div>
                <div>
                    <p class="border-b border-slate-200 pb-8"></p>
                    <p class="mt-2">توقيع المندوب الموصل</p>
                </div>
                <div>
                    <p class="border-b border-slate-200 pb-8"></p>
                    <p class="mt-2">توقيع مسؤول الجمعية</p>
                </div>
            </div>

            <!-- فوتر الوثيقة الخضراء اللطيف -->
            <div class="relative z-10 pt-10 text-center text-[10px] text-slate-400">
                <p>هذه الوثيقة صادرة ومعتمدة إلكترونياً من منصة جود اللوجستية لحفظ النعمة.</p>
                <p class="mt-1">نشكركم على مساهمتكم الإنسانية والبيئية الرائعة في تقليل هدر الغذاء وحفظ النعمة.</p>
            </div>

        </div>
    </div>

</body>
</html>