<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-900 leading-tight">
            {{ __('الملف الشخصي وإعدادات الحساب') }}
        </h2>
    </x-slot>

    <div class="py-6 space-y-8 max-w-4xl mx-auto sm:px-6 lg:px-8">
        
        <!-- عرض التنبيهات وإشعارات النجاح الفخمة -->
        @if (session('status') === 'profile-updated')
            <div class="bg-emerald-50 border-r-4 border-emerald-500 p-4 rounded-xl shadow-sm text-emerald-800 font-bold text-sm">
                تم تحديث بيانات ملفك الشخصي وصورتك بنجاح!
            </div>
        @endif
        @if (session('status') === 'password-updated')
            <div class="bg-emerald-50 border-r-4 border-emerald-500 p-4 rounded-xl shadow-sm text-emerald-800 font-bold text-sm">
                تم تحديث كلمة المرور بنجاح! يرجى استخدامها في تسجيلات الدخول القادمة.
            </div>
        @endif

        <!-- النموذج الرئيسي الشامل لتعديل البيانات وتحديث الصورة -->
        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-8">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PATCH')

                <!-- قسم الأفاتار التفاعلي مع ميزة المعاينة قبل الحفظ -->
                <div class="flex flex-col items-center space-y-4 pb-6 border-b border-slate-100">
                    <div class="relative group">
                        <!-- عرض الصورة الحالية أو الحرف الأول للمستخدم -->
                        <div class="w-32 h-32 rounded-3xl overflow-hidden border-2 border-emerald-500/20 shadow-md flex items-center justify-center bg-emerald-50 text-emerald-600">
                            @if($user->profile?->avatar)
                                <img id="avatar-preview" class="w-full h-full object-cover" src="{{ asset('storage/' . $user->profile->avatar) }}" alt="{{ $user->name }}">
                            @else
                                <div id="avatar-placeholder" class="text-4xl font-extrabold">
                                    {{ mb_substr($user->name, 0, 1) }}
                                </div>
                                <img id="avatar-preview" class="w-full h-full object-cover hidden" src="#" alt="Preview">
                            @endif
                        </div>
                        
                        <!-- زر الكاميرا للرفع المباشر -->
                        <label for="avatar" class="absolute bottom-1 left-1 bg-emerald-600 hover:bg-emerald-700 text-white p-2.5 rounded-2xl shadow-md border-2 border-white cursor-pointer transition-all hover:scale-105">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </label>
                        <input type="file" id="avatar" name="avatar" class="hidden" accept="image/*" onchange="previewImage(this)">
                    </div>
                    <div class="text-center">
                        <h3 class="text-sm font-bold text-slate-800">صورة الحساب الشخصية</h3>
                        <p class="text-[10px] text-slate-400 mt-1">يدعم صيغ JPG, PNG بحجم أقصى 2 ميجابايت</p>
                        <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
                    </div>
                </div>

                <!-- 1. البيانات الأساسية (اسم وميل) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="name" :value="__('الاسم الشخصي أو اسم المسؤول')" class="text-xs font-bold text-slate-700" />
                        <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('البريد الإلكتروني')" class="text-xs font-bold text-slate-700" />
                        <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" :value="old('email', $user->email)" required autocomplete="username" />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>
                </div>

                <!-- 2. بيانات الاتصال والمؤسسة (تظهر حسب دور الحساب ليكون البروفايل مخصصاً وذكياً) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-slate-100">
                    <div>
                        <x-input-label for="phone" :value="__('رقم الهاتف للتواصل')" class="text-xs font-bold text-slate-700" />
                        <x-text-input id="phone" name="phone" type="text" class="block mt-1 w-full" :value="old('phone', $user->profile?->phone)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                    </div>

                    @if($user->role === 'donor' || $user->role === 'receiver')
                        <div>
                            <x-input-label for="organization_name" :value="__('اسم المنشأة أو الجمعية')" class="text-xs font-bold text-slate-700" />
                            <x-text-input id="organization_name" name="organization_name" type="text" class="block mt-1 w-full" :value="old('organization_name', $user->profile?->organization_name)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('organization_name')" />
                        </div>
                    @endif

                    @if($user->role === 'receiver')
                        <div>
                            <x-input-label for="license_number" :value="__('رقم ترخيص الجمعية الرسمي')" class="text-xs font-bold text-slate-700" />
                            <x-text-input id="license_number" name="license_number" type="text" class="block mt-1 w-full" :value="old('license_number', $user->profile?->license_number)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('license_number')" />
                        </div>
                    @endif

                    @if($user->role === 'donor' || $user->role === 'receiver')
                        <div class="md:col-span-2">
                            <x-input-label for="address" :value="__('العنوان المفصل لمقر الاستلام والتسليم')" class="text-xs font-bold text-slate-700" />
                            <x-text-input id="address" name="address" type="text" class="block mt-1 w-full" :value="old('address', $user->profile?->address)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('address')" />
                        </div>
                    @endif
                </div>

                <!-- زر حفظ البيانات الكلي -->
                <div class="flex justify-end pt-4 border-t border-slate-100">
                    <x-primary-button class="bg-emerald-600 hover:bg-emerald-700 py-2.5 px-6 rounded-xl shadow-md shadow-emerald-50">
                        حفظ البيانات الحالية
                    </x-primary-button>
                </div>
            </form>
        </div>

        <!-- 3. كرت تحديث كلمة المرور المنفصل للأمان والحماية -->
        <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-100 p-8">
            <h3 class="text-lg font-bold text-slate-900 mb-2">تحديث كلمة المرور</h3>
            <p class="text-xs text-slate-400 mb-6">احرص على استخدام كلمة مرور قوية لتأمين حسابك وحفظ بيانات المنشأة.</p>

            <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <x-input-label for="update_password_current_password" :value="__('كلمة المرور الحالية')" class="text-xs font-bold text-slate-700" />
                        <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" placeholder="••••••••" required />
                        <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="update_password_password" :value="__('كلمة المرور الجديدة')" class="text-xs font-bold text-slate-700" />
                        <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" placeholder="••••••••" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="update_password_password_confirmation" :value="__('تأكيد كلمة المرور الجديدة')" class="text-xs font-bold text-slate-700" />
                        <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" placeholder="••••••••" required />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-slate-100">
                    <x-primary-button class="bg-indigo-600 hover:bg-indigo-700 py-2.5 px-6 rounded-xl shadow-md">
                        تحديث كلمة المرور
                    </x-primary-button>
                </div>
            </form>
        </div>

    </div>

    <!-- كود جافا سكريبت لمعاينة الصورة فورياً للعميل بمجرد اختيارها من جواله أو لابتوبه -->
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    // إخفاء الـ placeholder النصي وإظهار عنصر الصورة ببياناتها الجديدة
                    var placeholder = document.getElementById('avatar-placeholder');
                    var preview = document.getElementById('avatar-preview');
                    
                    if (placeholder) placeholder.classList.add('hidden');
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-app-layout>