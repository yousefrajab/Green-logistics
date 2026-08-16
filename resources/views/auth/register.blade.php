<x-guest-layout>

    <div x-data="{ role: '{{ old('role', 'donor') }}' }" class="w-full">

        <!-- ================= HEADER ================= -->
        <div class="fade-up mb-7">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-bold text-emerald-700">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                حساب جديد
            </span>
            <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900">إنشاء حساب جديد</h2>
            <p class="mt-2 text-sm leading-7 text-slate-500">انضم إلينا اليوم وساهم في إنقاذ وحفظ الطعام الفائض.</p>
        </div>

        <!-- ================= FORM ================= -->
        <form method="POST" action="{{ route('register') }}" class="fade-up fade-up-delay-1 space-y-8">
            @csrf

            <!-- ================= SECTION: ACCOUNT TYPE ================= -->
            <div>
                <div class="mb-3 flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </span>
                    <h3 class="text-sm font-bold text-slate-700">نوع الحساب</h3>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">

                    <!-- Donor -->
                    <button type="button" @click="role = 'donor'"
                        :class="role === 'donor'
                            ? 'border-emerald-500 bg-emerald-50/60 text-emerald-700 ring-2 ring-emerald-500'
                            : 'border-slate-200 bg-white text-slate-500 hover:border-slate-300'"
                        class="relative flex min-h-[140px] flex-col justify-between rounded-2xl border p-4 text-right transition-all"
                    >
                        <svg x-cloak x-show="role === 'donor'" class="absolute left-3 top-3 h-5 w-5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M12 2a10 10 0 100 20 10 10 0 000-20zm4.7 7.7l-5.4 5.4a1 1 0 01-1.4 0l-2.6-2.6a1 1 0 111.4-1.4l1.9 1.9 4.7-4.7a1 1 0 111.4 1.4z" clip-rule="evenodd" />
                        </svg>
                        <div class="flex w-full justify-end">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 21h18M5 21V9h14v12M8 9V5h8v4" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-sm font-bold">جهة متبرعة</p>
                            <p class="mt-0.5 text-[11px] text-slate-400">مطعم / فندق / متجر</p>
                        </div>
                    </button>

                    <!-- Receiver -->
                    <button type="button" @click="role = 'receiver'"
                        :class="role === 'receiver'
                            ? 'border-emerald-500 bg-emerald-50/60 text-emerald-700 ring-2 ring-emerald-500'
                            : 'border-slate-200 bg-white text-slate-500 hover:border-slate-300'"
                        class="relative flex min-h-[140px] flex-col justify-between rounded-2xl border p-4 text-right transition-all"
                    >
                        <svg x-cloak x-show="role === 'receiver'" class="absolute left-3 top-3 h-5 w-5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M12 2a10 10 0 100 20 10 10 0 000-20zm4.7 7.7l-5.4 5.4a1 1 0 01-1.4 0l-2.6-2.6a1 1 0 111.4-1.4l1.9 1.9 4.7-4.7a1 1 0 111.4 1.4z" clip-rule="evenodd" />
                        </svg>
                        <div class="flex w-full justify-end">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M4 21h16M6 21V8l6-4 6 4v13M9 21v-5h6v5" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-sm font-bold">جهة مستلمة</p>
                            <p class="mt-0.5 text-[11px] text-slate-400">جمعية / مستودع</p>
                        </div>
                    </button>

                    <!-- Driver -->
                    <button type="button" @click="role = 'driver'"
                        :class="role === 'driver'
                            ? 'border-emerald-500 bg-emerald-50/60 text-emerald-700 ring-2 ring-emerald-500'
                            : 'border-slate-200 bg-white text-slate-500 hover:border-slate-300'"
                        class="relative flex min-h-[140px] flex-col justify-between rounded-2xl border p-4 text-right transition-all"
                    >
                        <svg x-cloak x-show="role === 'driver'" class="absolute left-3 top-3 h-5 w-5 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M12 2a10 10 0 100 20 10 10 0 000-20zm4.7 7.7l-5.4 5.4a1 1 0 01-1.4 0l-2.6-2.6a1 1 0 111.4-1.4l1.9 1.9 4.7-4.7a1 1 0 111.4 1.4z" clip-rule="evenodd" />
                        </svg>
                        <div class="flex w-full justify-end">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 7h11v10H3zM14 10h4l3 3v4h-7zM7 20a2 2 0 100-4 2 2 0 000 4zm11 0a2 2 0 100-4 2 2 0 000 4z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-sm font-bold">مندوب توصيل</p>
                            <p class="mt-0.5 text-[11px] text-slate-400">متطوع / شركة توصيل</p>
                        </div>
                    </button>
                </div>

                <!-- القيمة البرمجية التي سترسل تلقائياً إلى قاعدة البيانات -->
                <input type="hidden" name="role" :value="role">

                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>

            <!-- ================= SECTION: PERSONAL INFO ================= -->
            <div>
                <div class="mb-3 flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1118.88 6.196 9 9 0 015.12 17.804zM15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </span>
                    <h3 class="text-sm font-bold text-slate-700">البيانات الشخصية</h3>
                </div>

                <div class="space-y-5">
                    <div>
                        <x-input-label for="name" :value="__('الاسم الشخصي أو اسم المسؤول')" class="mb-2 font-semibold" />
                        <x-text-input id="name" class="input-modern block w-full rounded-xl border-slate-200 py-3.5 focus:border-emerald-500 focus:ring-emerald-500" type="text" name="name" :value="old('name')" required placeholder="اكتب اسم المسؤول" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <x-input-label for="email" :value="__('البريد الإلكتروني للعمل')" class="mb-2 font-semibold" />
                            <x-text-input id="email" class="input-modern block w-full rounded-xl border-slate-200 py-3.5 focus:border-emerald-500 focus:ring-emerald-500" type="email" name="email" :value="old('email')" required placeholder="email@example.com" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="phone" :value="__('رقم الهاتف للتواصل الميداني')" class="mb-2 font-semibold" />
                            <x-text-input id="phone" class="input-modern block w-full rounded-xl border-slate-200 py-3.5 focus:border-emerald-500 focus:ring-emerald-500" type="text" name="phone" :value="old('phone')" required placeholder="05xxxxxxxx" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= SECTION: ORGANIZATION ================= -->
            <div x-show="role === 'donor' || role === 'receiver'" x-transition x-cloak>
                <div class="mb-3 flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V9h14v12M8 9V5h8v4m-6 4h.01M8 17h.01M12 13h.01M12 17h.01M16 13h.01M16 17h.01" />
                        </svg>
                    </span>
                    <h3 class="text-sm font-bold text-slate-700">بيانات المنشأة</h3>
                </div>

                <div class="space-y-5">
                    <div>
                        <x-input-label for="organization_name" :value="__('اسم المنشأة أو اسم الجمعية')" class="mb-2 font-semibold" />
                        <x-text-input id="organization_name" class="input-modern block w-full rounded-xl border-slate-200 py-3.5 focus:border-emerald-500 focus:ring-emerald-500" type="text" name="organization_name" :value="old('organization_name')" placeholder="مثال: فندق الشيراتون أو جمعية الإغاثة" />
                        <x-input-error :messages="$errors->get('organization_name')" class="mt-2" />
                    </div>

                    <div x-show="role === 'receiver'" x-transition x-cloak>
                        <x-input-label for="license_number" :value="__('رقم ترخيص الجمعية الرسمي')" class="mb-2 font-semibold" />
                        <x-text-input id="license_number" class="input-modern block w-full rounded-xl border-slate-200 py-3.5 focus:border-emerald-500 focus:ring-emerald-500" type="text" name="license_number" :value="old('license_number')" placeholder="رقم القيد أو الترخيص الحكومي" />
                        <x-input-error :messages="$errors->get('license_number')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="address" :value="__('العنوان المفصل لمقر الاستلام')" class="mb-2 font-semibold" />
                        <x-text-input id="address" class="input-modern block w-full rounded-xl border-slate-200 py-3.5 focus:border-emerald-500 focus:ring-emerald-500" type="text" name="address" :value="old('address')" placeholder="المدينة، الشارع، علامة مميزة" />
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- ================= SECTION: ACCOUNT STATUS ================= -->
            <div>
                <div class="mb-3 flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 3c-2.756 0-5.29.936-7.31 2.506" />
                        </svg>
                    </span>
                    <h3 class="text-sm font-bold text-slate-700">حالة الحساب</h3>
                </div>

                <div class="relative">
                    <select id="status" name="status" class="input-modern block w-full appearance-none rounded-xl border-slate-200 py-3.5 pl-4 pr-10 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="active">نشط وموثق مباشرة</option>
                        <option value="pending_verification">بانتظار التفعيل والمراجعة</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
            </div>

            <!-- ================= LOCATION ================= -->
            <input type="hidden" name="latitude" id="latitude" value="31.5000">
            <input type="hidden" name="longitude" id="longitude" value="34.4667">

            <!-- ================= SECTION: SECURITY ================= -->
            <div>
                <div class="mb-3 flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </span>
                    <h3 class="text-sm font-bold text-slate-700">كلمة المرور</h3>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <x-input-label for="password" :value="__('كلمة المرور')" class="mb-2 font-semibold" />
                        <x-text-input id="password" class="input-modern block w-full rounded-xl border-slate-200 py-3.5 focus:border-emerald-500 focus:ring-emerald-500" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="password_confirmation" :value="__('تأكيد كلمة المرور')" class="mb-2 font-semibold" />
                        <x-text-input id="password_confirmation" class="input-modern block w-full rounded-xl border-slate-200 py-3.5 focus:border-emerald-500 focus:ring-emerald-500" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- ================= REGISTER BUTTON ================= -->
            <div class="pt-1">
                <x-primary-button class="flex w-full justify-center rounded-xl bg-emerald-600 py-3.5 text-base font-bold shadow-lg shadow-emerald-200 transition hover:bg-emerald-700 hover:shadow-emerald-300 active:scale-[.99]">
                    إنشاء الحساب والانضمام
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 12h14" />
                    </svg>
                </x-primary-button>
            </div>

            <!-- ================= LOGIN LINK ================= -->
            <div class="border-t border-slate-100 pt-6 text-center">
                <span class="text-sm text-slate-500">لديك حساب بالفعل؟</span>
                <a class="mr-1 text-sm font-bold text-emerald-600 transition hover:text-emerald-800" href="{{ route('login') }}">تسجيل الدخول</a>
            </div>
        </form>
    </div>
</x-guest-layout>