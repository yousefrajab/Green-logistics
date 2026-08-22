<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('الإعلان عن فائض طعام جديد') }}
        </h2>
    </x-slot>

    <!-- استدعاء مكتبة الخرائط العالمية المفتوحة Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <div class="py-12" dir="rtl" x-data="{ loadingAI: false }">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-8 relative">
                
                <!-- أنيميشن معالجة الصورة الذكي -->
                <div x-show="loadingAI" style="display: none;" class="absolute inset-0 bg-white/90 z-50 flex flex-col items-center justify-center space-y-4 rounded-2xl backdrop-blur-sm">
                    <div class="w-12 h-12 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-sm font-bold text-slate-800 animate-pulse">🪄 جاري فحص الصورة وتعبئة الإعلان تلقائياً بالذكاء الاصطناعي...</p>
                </div>

                <form method="POST" action="{{ route('listings.store') }}" enctype="multipart/form-data" id="listing-form" class="space-y-6">
                    @csrf

                    <!-- ميزة الكاميرا والتحليل البصري التلقائي المؤمنة الأبعاد بالكامل -->
                    <div class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-6 md:space-x-reverse pb-6 border-b border-slate-100">
                        
                        <!-- [تأمين الأبعاد الثابتة يدوياً]: مربع ثابت 128px بكسل ومقاوم للتمدد تحت أي ظرف [10] -->
                        <div style="width: 128px; height: 128px; min-width: 128px; min-height: 128px;" class="relative rounded-2xl overflow-hidden border-2 border-emerald-500/20 shadow-md flex items-center justify-center bg-slate-50 text-slate-400 shrink-0">
                            <div id="image-placeholder" class="text-xs text-center p-2 font-semibold">صورة الطعام الفائض</div>
                            <img id="image-preview" style="width: 100%; height: 100%; object-fit: cover;" class="hidden" src="#" alt="Preview">
                        </div>

                        <div class="space-y-3 text-right">
                            <h3 class="text-sm font-bold text-slate-800">صورة الطعام الفائض</h3>
                            <p class="text-[10px] text-slate-400 leading-normal">التقط صورة للوجبات الفائضة من جوالك، أو اختر صورة مجهزة. سيقوم الذكاء الاصطناعي بقرائتها بالكامل تلقائياً لتوفير وقتك!</p>
                            
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <label for="image" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl cursor-pointer transition-colors">
                                    📸 التقاط / اختيار صورة
                                </label>
                                <input type="file" id="image" name="image" class="hidden" accept="image/*" onchange="previewFoodImage(this)">

                                <button type="button" id="analyze-btn" onclick="triggerAIVision()" disabled class="inline-flex items-center px-4 py-2 bg-emerald-100 text-emerald-700 font-bold text-xs rounded-xl opacity-50 cursor-not-allowed transition-all">
                                    🪄 تحليل الصورة بالذكاء الاصطناعي
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- اسم الوجبة -->
                    <div>
                        <x-input-label for="title" :value="__('عنوان الإعلان (مثال: وجبات أرز مع دجاج فائضة)')" />
                        <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required placeholder="اكتب عنواناً واضحاً ومختصراً" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <!-- تفاصيل ومكونات الوجبة -->
                    <div>
                        <x-input-label for="description" :value="__('التفاصيل والمكونات ومسببات الحساسية')" />
                        <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="مثال: تحتوي الوجبات على أرز بسمتي ودجاج مشوي، تم حفظها مبردة في علب مغلقة وتكفي لـ..." >{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- الكمية -->
                        <div>
                            <x-input-label for="quantity" :value="__('الكمية (مثال: 15 وجبة، أو 10 كغ)')" />
                            <x-text-input id="quantity" class="block mt-1 w-full" type="text" name="quantity" :value="old('quantity')" required placeholder="اكتب الكمية وطريقة قياسها" />
                            <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                        </div>

                        <!-- التصنيف -->
                        <div>
                            <x-input-label for="category" :value="__('تصنيف الطعام')" />
                            <select id="category" name="category" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="cooked">مطبوخ (Cooked) - وجبات جاهزة ساخنة/باردة</option>
                                <option value="dry">جاف ومعلب (Dry/Canned) - مواد تموينية مغلفة</option>
                                <option value="fresh">طازج (Fresh) - خضار، فواكه، مخبوزات</option>
                            </select>
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- وقت انتهاء الصلاحية -->
                        <div>
                            <x-input-label for="expiry_time" :value="__('صالح للاستهلاك حتى تاريخ ووقت')" />
                            <x-text-input id="expiry_time" class="block mt-1 w-full" type="datetime-local" name="expiry_time" :value="old('expiry_time')" required />
                            <x-input-error :messages="$errors->get('expiry_time')" class="mt-2" />
                        </div>

                        <!-- عنوان الاستلام الفعلي (يتعبأ تلقائياً عند تحديد الخريطة!) -->
                        <div>
                            <x-input-label for="address" :value="__('العنوان الجغرافي المكتوب (يتعبأ تلقائياً من الخريطة)')" />
                            <x-text-input id="address" class="block mt-1 w-full bg-slate-50 font-bold border border-slate-200" type="text" name="address" :value="old('address', $user->profile->address ?? '')" required placeholder="يرجى الضغط على الخريطة لتوليد العنوان تلقائياً" />
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>
                    </div>

                    <!-- خريطة محدد الموقع التفاعلية المؤمّنة الأبعاد -->
                    <div class="space-y-2 pt-4 border-t border-slate-100">
                        <x-input-label :value="__('موقع التبرع الجغرافي (تحدد الإحداثيات تلقائياً بـ GPS)')" class="font-bold text-slate-800" />
                        <!-- فرض الارتفاع صراحة كأقوى حماية لمنع تلاشي الخريطة [10] -->
                        <div id="picker-map" style="height: 320px; min-height: 320px; width: 100%;" class="rounded-2xl border border-slate-200 z-10 relative"></div>
                        <p class="text-[10px] text-slate-400 font-semibold">
                            💡 بمجرد الضغط في أي مكان على الخريطة أو سحب العلامة الزرقاء، سيقوم النظام تلقائياً بكتابة اسم الشارع والمنطقة الفعلية داخل حقل العنوان أعلاه وتحديث الـ GPS [2].
                        </p>
                    </div>

                    <!-- حقول خطوط العرض والطول المخفية -->
                    <input type="hidden" name="latitude" id="latitude" value="{{ $user->profile->latitude ?? 31.5000 }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ $user->profile->longitude ?? 34.4667 }}">

                    <!-- أزرار الإجراءات -->
                    <div class="flex items-center justify-end space-x-4 space-x-reverse pt-4 border-t border-gray-100">
                        <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900 focus:outline-none">
                            {{ __('إلغاء') }}
                        </a>
                        <x-primary-button class="bg-emerald-600 hover:bg-emerald-700">
                            {{ __('نشر الإعلان الآن') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- كود جافا سكريبت لمعاينة الصورة والتحليل البصري والتعديل الجغرافي -->
    <script>
        function previewFoodImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var placeholder = document.getElementById('image-placeholder');
                    var preview = document.getElementById('image-preview');
                    
                    if (placeholder) placeholder.classList.add('hidden');
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');

                    var analyzeBtn = document.getElementById('analyze-btn');
                    analyzeBtn.disabled = false;
                    analyzeBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    analyzeBtn.classList.add('bg-emerald-600', 'text-white', 'hover:bg-emerald-700', 'shadow-md');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function triggerAIVision() {
            var fileInput = document.getElementById('image');
            if (!fileInput.files || fileInput.files.length === 0) return;

            var alpineData = Alpine.$data(document.querySelector('[x-data]'));
            alpineData.loadingAI = true;

            var formData = new FormData();
            formData.append('image', fileInput.files[0]);

            fetch('{{ route('api.listings.analyze-image') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'فشلت عملية فحص الصورة بالذكاء الاصطناعي.');
                    });
                }
                return response.json();
            })
            .then(data => {
                // تعبئة البيانات الفردية المستقرة دون تداخل برمجى [4]
                if (data.title) document.getElementById('title').value = data.title;
                if (data.quantity) document.getElementById('quantity').value = data.quantity;
                if (data.category) document.getElementById('category').value = data.category;
                
                var desc = data.description || '';
                if (data.allergens) {
                    desc += "\n\n⚠️ " + data.allergens;
                }
                document.getElementById('description').value = desc;
            })
            .catch(error => {
                console.error('AI Vision Error:', error);
                
                // تصفير الصورة المرفوضة فوراً لمنع التلاعب [8]
                document.getElementById('image').value = "";
                var placeholder = document.getElementById('image-placeholder');
                var preview = document.getElementById('image-preview');
                
                if (preview) preview.classList.add('hidden');
                if (placeholder) placeholder.classList.remove('hidden');

                var analyzeBtn = document.getElementById('analyze-btn');
                analyzeBtn.disabled = true;
                analyzeBtn.classList.add('opacity-50', 'cursor-not-allowed');
                analyzeBtn.classList.remove('bg-emerald-600', 'text-white', 'hover:bg-emerald-700', 'shadow-md');

                alert(error.message);
            })
            .finally(() => {
                alpineData.loadingAI = false;
            });
        }

        // كود الخريطة
        document.addEventListener('DOMContentLoaded', function () {
            var defaultLat = parseFloat(document.getElementById('latitude').value) || 31.5000;
            var defaultLng = parseFloat(document.getElementById('longitude').value) || 34.4667;

            var pickerMap = L.map('picker-map').setView([defaultLat, defaultLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(pickerMap);

            var currentMarker = L.marker([defaultLat, defaultLng], {
                draggable: true
            }).addTo(pickerMap);

            function updateCoordinates(lat, lng) {
                document.getElementById('latitude').value = lat.toFixed(8);
                document.getElementById('longitude').value = lng.toFixed(8);
                document.getElementById('address').value = "جاري جلب وتحديد موقع العنوان الملاحي الجغرافي...";

                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=ar`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.display_name) {
                            document.getElementById('address').value = data.display_name;
                        } else {
                            document.getElementById('address').value = "إحداثيات مخصصة: " + lat.toFixed(4) + ", " + lng.toFixed(4);
                        }
                    })
                    .catch(error => {
                        console.error('Geocoding error:', error);
                        document.getElementById('address').value = "إحداثيات مخصصة: " + lat.toFixed(4) + ", " + lng.toFixed(4);
                    });
            }

            currentMarker.on('dragend', function (e) {
                var position = currentMarker.getLatLng();
                updateCoordinates(position.lat, position.lng);
            });

            pickerMap.on('click', function (e) {
                currentMarker.setLatLng(e.latlng);
                updateCoordinates(e.latlng.lat, e.latlng.lng);
            });
        });
    </script>
</x-app-layout>