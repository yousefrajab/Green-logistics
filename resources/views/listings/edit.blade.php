<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('تعديل إعلان الفائض') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100 p-8">
                
                <form method="POST" action="{{ route('listings.update', $listing->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT') <!-- نستخدم PUT لتحديث البيانات -->

                    <!-- اسم الوجبة -->
                    <div>
                        <x-input-label for="title" :value="__('عنوان الإعلان')" />
                        <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $listing->title)" required autofocus />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <!-- تفاصيل ومكونات الوجبة -->
                    <div>
                        <x-input-label for="description" :value="__('التفاصيل والمكونات (اختياري)')" />
                        <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $listing->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- الكمية -->
                        <div>
                            <x-input-label for="quantity" :value="__('الكمية')" />
                            <x-text-input id="quantity" class="block mt-1 w-full" type="text" name="quantity" :value="old('quantity', $listing->quantity)" required />
                            <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                        </div>

                        <!-- التصنيف -->
                        <div>
                            <x-input-label for="category" :value="__('تصنيف الطعام')" />
                            <select id="category" name="category" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="cooked" {{ $listing->category == 'cooked' ? 'selected' : '' }}>مطبوخ (Cooked)</option>
                                <option value="dry" {{ $listing->category == 'dry' ? 'selected' : '' }}>جاف ومعلب (Dry/Canned)</option>
                                <option value="fresh" {{ $listing->category == 'fresh' ? 'selected' : '' }}>طازج (Fresh)</option>
                            </select>
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- وقت انتهاء الصلاحية -->
                        <div>
                            <x-input-label for="expiry_time" :value="__('صالح للاستهلاك حتى تاريخ ووقت')" />
                            <x-text-input id="expiry_time" class="block mt-1 w-full" type="datetime-local" name="expiry_time" :value="old('expiry_time', $listing->expiry_time->format('Y-m-d\TH:i'))" required />
                            <x-input-error :messages="$errors->get('expiry_time')" class="mt-2" />
                        </div>

                        <!-- عنوان الاستلام -->
                        <div>
                            <x-input-label for="address" :value="__('عنوان ومكان الاستلام')" />
                            <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address', $listing->address)" required />
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>
                    </div>

                    <!-- أزرار الإجراءات -->
                    <div class="flex items-center justify-end space-x-4 space-x-reverse pt-4 border-t border-gray-100">
                        <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('إلغاء') }}
                        </a>
                        <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">
                            {{ __('تحديث الإعلان') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>