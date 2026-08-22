<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('تعديل شحنة الفائض (سلة الأطعمة)') }}
        </h2>
    </x-slot>

    <!-- استدعاء مكتبة الخرائط العالمية المفتوحة Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- نفعّل الـ Alpine.js ونقوم بجلب وحقن الأصناف الحالية المخزنة مسبقاً بقاعدة البيانات داخل المصفوفة تلقائياً! [6] -->
    <div class="py-12" dir="rtl" x-data="{ 
        loadingAI: false,
        items: [
            @foreach($listing->items as $item)
                { 
                    title: '{{ $item->title }}', 
                    quantity: '{{ $item->quantity }}', 
                    category: '{{ $item->category }}', 
                    expiry_time: '{{ $item->expiry_time->format('Y-m-d\TH:i') }}', 
                    description: '{{ $item->description }}' 
                },
            @endforeach
        ],
        addItem() {
            this.items.push({ title: '', quantity: '', category: 'cooked', expiry_time: '', description: '' });
        },
        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
            }
        },
        // [تكامل وحل فني]: دالة الـ AI تعمل بداخل نطاق الـ Component بصفحة التعديل أيضاً لسلامة التفاعلية [6]
        triggerAIVision() {
            var fileInput = document.getElementById('image');
            if (!fileInput.files || fileInput.files.length === 0) return;

            this.loadingAI = true;
            var self = this;

            var formData = new FormData();
            formData.append('image', fileInput.files[0]);

            fetch('{{ route('api.listings.analyze-image') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=&quot;csrf-token&quot;]').getAttribute('content')
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
                if (data.title) self.items[0].title = data.title;
                if (data.quantity) self.items[0].quantity = data.quantity;
                if (data.category) self.items[0].category = data.category;
                
                var desc = data.description || '';
                if (data.allergens) {
                    desc += '\n\n⚠️ ' + data.allergens;
                }
                self.items[0].description = desc;
            })
            .catch(error => {
                console.error('AI Vision Error:', error);
                document.getElementById('image').value = '';
                var placeholder = document.getElementById('image-placeholder');
                var preview = document.getElementById('image-preview');
                
                var originalSrc = preview.getAttribute('data-original-src');
                if (originalSrc) {
                    preview.src = originalSrc;
                    preview.classList.remove('hidden');
                } else {
                    preview.classList.add('hidden');
                    if (placeholder) placeholder.classList.remove('hidden');
                }

                var analyzeBtn = document.getElementById('analyze-btn');
                analyzeBtn.disabled = true;
                analyzeBtn.classList.add('opacity-50', 'cursor-not-allowed');
                analyzeBtn.classList.remove('bg-emerald-600', 'text-white', 'hover:bg-emerald-700', 'shadow-md');

                alert(error.message);
            })
            .finally(() => {
                self.loadingAI = false;
            });
        }
    }">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-8 relative">
                
                <!-- أنيميشن معالجة الصورة الذكي -->
                <div x-show="loadingAI" style="display: none;" class="absolute inset-0 bg-white/90 z-50 flex flex-col items-center justify-center space-y-4 rounded-2xl backdrop-blur-sm">
                    <div class="w-12 h-12 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
                    <p class="text-sm font-bold text-slate-800 animate-pulse">🪄 جاري قراءة الصورة بالذكاء الاصطناعي وتحديث الصنف الأول تلقائياً...</p>
                </div>

                <form method="POST" action="{{ route('listings.update', $listing->id) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- ميزة الكاميرا والتحليل البصري التلقائي المؤمنة الأبعاد تماماً بصفحة التعديل -->
                    <div class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-6 md:space-x-reverse pb-6 border-b border-slate-100">
                        
                        <!-- [تأمين الأبعاد الثابتة يدوياً]: مربع ثابت 128px بكسل ومقاوم للتمدد تحت أي ظرف [10] -->
                        <div style="width: 128px; height: 128px; min-width: 128px; min-height: 128px;" class="relative rounded-2xl overflow-hidden border-2 border-emerald-500/20 shadow-md flex items-center justify-center bg-slate-50 text-slate-400 shrink-0">
                            @if($listing->image_path)
                                <img id="image-preview" style="width: 100%; height: 100%; object-fit: cover;" src="{{ asset('storage/' . $listing->image_path) }}" alt="{{ $listing->title }}" data-original-src="{{ asset('storage/' . $listing->image_path) }}">
                            @else
                                <div id="image-placeholder" class="text-xs text-center p-2 font-semibold">صورة الشحنة الكلية</div>
                                <img id="image-preview" style="width: 100%; height: 100%; object-fit: cover;" class="hidden" src="#" alt="Preview">
                            @endif
                        </div>

                        <div class="space-y-3 text-right">
                            <h3 class="text-sm font-bold text-slate-800">صورة الأطعمة الكلية</h3>
                            <p class="text-[10px] text-slate-400 leading-normal">يمكنك استبدال صورة الطعام الكلية، واستخدام ميزة التحليل البصري الذكية لتحديث وتعبئة الصنف الأول تلقائياً!</p>
                            
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <label for="image" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl cursor-pointer transition-colors">
                                    📸 استبدال الصورة
                                </label>
                                <input type="file" id="image" name="image" class="hidden" accept="image/*" onchange="previewFoodImage(this)">

                                <button type="button" id="analyze-btn" @click="triggerAIVision()" disabled class="inline-flex items-center px-4 py-2 bg-emerald-100 text-emerald-700 font-bold text-xs rounded-xl opacity-50 cursor-not-allowed transition-all">
                                    🪄 تحليل الصورة بالذكاء الاصطناعي
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- سلة الأطعمة التفاعلية الديناميكية بصفحة التعديل (تعرض وتحدث كافة الأصناف) -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-extrabold text-slate-800">سلة الأطعمة الفائضة بالشحنة (تعديل الأصناف)</h3>
                        
                        <div class="space-y-6">
                            <template x-for="(item, index) in items" :key="index">
                                <!-- بطاقة الصنف الفرعي التفاعلية المحدثة -->
                                <div class="p-5 bg-slate-50/50 border border-slate-200/60 rounded-2xl space-y-4 relative">
                                    
                                    <!-- زر إزالة الصنف من السلة -->
                                    <button type="button" x-show="items.length > 1" @click="removeItem(index)" class="absolute top-4 left-4 text-xs font-bold text-rose-500 hover:text-rose-700 transition-colors focus:outline-none">
                                        حذف الصنف ×
                                    </button>

                                    <h4 class="text-xs font-black text-slate-400 flex items-center">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full ml-1.5"></span>
                                        صنف طعام فرعي (#<span x-text="index + 1"></span>)
                                    </h4>

                                    <!-- اسم الصنف ومكوناته -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">اسم وجبة الطعام</label>
                                            <input type="text" :name="'items['+index+'][title]'" x-model="item.title" required class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-xs" placeholder="مثال: كبسة دجاج مع الأرز">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">الكمية التقديرية للوجبة</label>
                                            <input type="text" :name="'items['+index+'][quantity]'" x-model="item.quantity" required class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-xs" placeholder="مثال: صينية كبيرة (تكفي لـ 15 شخصاً)">
                                        </div>
                                    </div>

                                    <!-- المكونات ومسببات الحساسية للصنف -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">التفاصيل ومسببات الحساسية لهذا الصنف</label>
                                        <textarea :name="'items['+index+'][description]'" x-model="item.description" rows="2" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-xs" placeholder="اكتب المكونات ومسببات الحساسية المكتشفة..."></textarea>
                                    </div>

                                    <!-- التصنيف والانتهاء الخاص بكل صنف -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">تصنيف الصنف</label>
                                            <select :name="'items['+index+'][category]'" x-model="item.category" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-xs">
                                                <option value="cooked">مطبوخ (Cooked)</option>
                                                <option value="dry">جاف ومعلب (Dry/Canned)</option>
                                                <option value="fresh">طازج (Fresh)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1">صالح للاستهلاك لغاية</label>
                                            <input type="datetime-local" :name="'items['+index+'][expiry_time]'" x-model="item.expiry_time" required class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-xs">
                                        </div>
                                    </div>

                                </div>
                            </template>
                        </div>

                        <!-- زر إضافة صنف طعام فرعي إضافي للسلة -->
                        <div class="pt-2 flex justify-start">
                            <button type="button" @click="addItem()" class="inline-flex items-center px-4 py-2 border border-slate-200 text-xs font-bold rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 transition-colors focus:outline-none">
                                ➕ إضافة صنف طعام آخر لهذه الشحنة
                            </button>
                        </div>
                    </div>

                    <!-- عنوان ومكان الاستلام المشترك للشحنة -->
                    <div>
                        <x-input-label for="address" :value="__('العنوان الجغرافي المكتوب ومكان الاستلام')" class="font-bold text-slate-800 mb-1" />
                        <x-text-input id="address" class="block w-full bg-slate-50 font-bold border border-slate-200" type="text" name="address" :value="old('address', $listing->address)" required />
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <!-- [تأمين ارتفاع الخريطة بـ 320px يدوياً لمنع الاختفاء التام تحت أي ظرف] [10] -->
                    <div class="space-y-2 pt-4 border-t border-slate-100">
                        <x-input-label :value="__('تعديل الموقع الجغرافي للوجبة (تحديد موقع الـ GPS الفعلي)')" class="font-bold text-slate-800" />
                        <!-- فرض الارتفاع صراحة لحماية بقاء الخريطة دائماً [10] -->
                        <div id="picker-map" style="height: 320px; min-height: 320px; width: 100%;" class="rounded-2xl border border-slate-200 z-10 relative"></div>
                        <p class="text-[10px] text-slate-400 font-semibold">
                            💡 بإمكانك سحب العلامة الزرقاء أو الضغط على الخريطة لتعديل الإحداثيات الجغرافية، وسيتم كتابة اسم الشارع والمنطقة وتحديث الـ GPS تلقائياً [2].
                        </p>
                    </div>

                    <!-- حقول خطوط العرض والطول المخفية للتحديث الفعلي في السيرفر -->
                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $listing->latitude ?? 31.5000) }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $listing->longitude ?? 34.4667) }}">

                    <!-- أزرار الإجراءات -->
                    <div class="flex items-center justify-end space-x-4 space-x-reverse pt-4 border-t border-gray-100">
                        <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900 focus:outline-none">
                            {{ __('إلغاء') }}
                        </a>
                        <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">
                            {{ __('تحديث الشحنة وكافة الأصناف') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- كود جافا سكريبت المطور بصفحة التعديل -->
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

        // كود الخريطة الافتراضي للتعديل
        document.addEventListener('DOMContentLoaded', function () {
            var currentLat = parseFloat(document.getElementById('latitude').value) || 31.5000;
            var currentLng = parseFloat(document.getElementById('longitude').value) || 34.4667;

            var pickerMap = L.map('picker-map').setView([currentLat, currentLng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(pickerMap);

            var currentMarker = L.marker([currentLat, currentLng], {
                draggable: true
            }).addTo(pickerMap);

            function updateCoordinates(lat, lng) {
                document.getElementById('latitude').value = lat.toFixed(8);
                document.getElementById('longitude').value = lng.toFixed(8);
                document.getElementById('address').value = "جاري جلب وتحديد موقع العنوان الملاحي الجغرافي الجديد...";

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