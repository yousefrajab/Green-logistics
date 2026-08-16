<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('الإعلان عن فائض طعام جديد') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100 p-8">
                
                <form method="POST" action="{{ route('listings.store') }}" class="space-y-6">
                    @csrf

                    <!-- اسم الوجبة / الفائض -->
                    <div>
                        <x-input-label for="title" :value="__('عنوان الإعلان (مثال: وجبات أرز مع دجاج فائضة)')" />
                        <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus placeholder="اكتب عنواناً واضحاً ومختصراً" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <!-- تفاصيل ومكونات الوجبة -->
                    <div>
                        <x-input-label for="description" :value="__('التفاصيل والمكونات (اختياري)')" />
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

                        <!-- عنوان الاستلام الفعلي (افتراضياً نضع عنوان ملف المتبرع) -->
                        <div>
                            <x-input-label for="address" :value="__('عنوان ومكان الاستلام')" />
                            <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address', $user->profile->address ?? '')" required placeholder="اكتب مكان تواجد الطعام بوضوح للمندوب" />
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>
                    </div>

                    <!-- أزرار الإجراءات -->
                    <div class="flex items-center justify-end space-x-4 space-x-reverse pt-4 border-t border-gray-100">
                        <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            {{ __('إلغاء') }}
                        </a>
                        <x-primary-button class="bg-green-600 hover:bg-green-700 focus:ring-green-500">
                            {{ __('نشر الإعلان الآن') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>