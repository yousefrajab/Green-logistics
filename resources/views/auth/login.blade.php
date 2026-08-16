<x-guest-layout>

    <!-- ================= HEADER ================= -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-extrabold tracking-tight text-slate-900">مرحباً بك مجدداً</h2>
                <p class="mt-2 text-sm leading-7 text-slate-500">سجل الدخول للوصول إلى لوحة التحكم الخاصة بك.</p>
            </div>

            <!-- Theme -->
            <button type="button" class="hidden h-11 w-11 items-center justify-center rounded-full border border-slate-200 text-slate-500 transition hover:bg-slate-50 sm:flex">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 7a5 5 0 100 10 5 5 0 000-10z" />
                </svg>
            </button>
        </div>
    </div>

    <!-- ================= SESSION STATUS ================= -->
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <!-- ================= LOGIN FORM ================= -->
    <!-- التبويبات المحدثة والمطابقة لصفحة التسجيل لتجنب أي تشتت -->
    <div x-data="{ activeRole: 'donor' }">
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- ================= ACCOUNT TYPE ================= -->
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">نوع الحساب</label>
                <div class="grid grid-cols-3 overflow-hidden rounded-xl border border-slate-200 bg-white">
                    
                    <!-- Donor (جهة متبرعة) -->
                    <button type="button" @click="activeRole = 'donor'"
                        :class="activeRole === 'donor' ? 'bg-emerald-50 text-emerald-700 border-l border-emerald-500' : 'text-slate-500 hover:bg-slate-50 border-l border-slate-200'"
                        class="flex min-h-[74px] flex-col items-center justify-center gap-1 transition"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 21h18M5 21V9h14v12M8 9V5h8v4" />
                        </svg>
                        <span class="text-xs font-bold">جهة متبرعة</span>
                    </button>

                    <!-- Receiver (جهة مستلمة) -->
                    <button type="button" @click="activeRole = 'receiver'"
                        :class="activeRole === 'receiver' ? 'bg-emerald-50 text-emerald-700 border-l border-emerald-500' : 'text-slate-500 hover:bg-slate-50 border-l border-slate-200'"
                        class="flex min-h-[74px] flex-col items-center justify-center gap-1 transition"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M4 21h16M6 21V8l6-4 6 4v13M9 21v-5h6v5" />
                        </svg>
                        <span class="text-xs font-semibold">جهة مستلمة</span>
                    </button>

                    <!-- Driver (مندوب توصيل) -->
                    <button type="button" @click="activeRole = 'driver'"
                        :class="activeRole === 'driver' ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-50'"
                        class="flex min-h-[74px] flex-col items-center justify-center gap-1 transition"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 7h11v10H3zM14 10h4l3 3v4h-7zM7 20a2 2 0 100-4 2 2 0 000 4zm11 0a2 2 0 100-4 2 2 0 000 4z" />
                        </svg>
                        <span class="text-xs font-semibold">مندوب توصيل</span>
                    </button>
                </div>
            </div>

            <!-- ================= EMAIL ================= -->
            <div>
                <x-input-label for="email" :value="__('البريد الإلكتروني')" class="mb-2 font-semibold" />
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 7l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <x-text-input id="email" class="input-modern block w-full rounded-xl border-slate-200 py-3.5 pr-12 pl-4 focus:border-emerald-500 focus:ring-emerald-500" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="name@company.com" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- ================= PASSWORD ================= -->
            <div>
                <div class="mb-2 flex items-center justify-between">
                    <x-input-label for="password" :value="__('كلمة المرور')" class="font-semibold" />
                    @if (Route::has('password.request'))
                        <a class="text-xs font-semibold text-emerald-600 transition hover:text-emerald-800" href="{{ route('password.request') }}">نسيت كلمة المرور؟</a>
                    @endif
                </div>

                <div class="relative" x-data="{ show: false }">
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>

                    <x-text-input id="password" ::type="show ? 'text' : 'password'" class="input-modern block w-full rounded-xl border-slate-200 py-3.5 pr-12 pl-12 focus:border-emerald-500 focus:ring-emerald-500" name="password" required autocomplete="current-password" placeholder="••••••••" />

                    <button type="button" @click="show = !show" class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z" />
                            <circle cx="12" cy="12" r="2.5"/>
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- ================= REMEMBER ================= -->
            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex cursor-pointer items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-emerald-600 shadow-sm focus:ring-emerald-500" name="remember">
                    <span class="mr-2 text-sm text-slate-500">تذكرني في المرة القادمة</span>
                </label>
            </div>

            <!-- ================= LOGIN BUTTON ================= -->
            <div class="pt-1">
                <x-primary-button class="flex w-full justify-center rounded-xl bg-emerald-600 py-3.5 text-base font-bold shadow-lg shadow-emerald-200 transition hover:bg-emerald-700 hover:shadow-emerald-300">
                    تسجيل الدخول
                    <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 12h14" />
                    </svg>
                </x-primary-button>
            </div>

            <!-- ================= SOCIAL LOGIN ================= -->
            <div class="relative py-3">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-100"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-white px-4 text-xs text-slate-400">أو سجل الدخول باستخدام</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <button type="button" class="flex items-center justify-center gap-2 rounded-xl border border-slate-200 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    <span class="font-bold text-lg">G</span>
                    Google
                </button>
                <button type="button" class="flex items-center justify-center gap-2 rounded-xl border border-slate-200 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    <span class="font-bold text-lg">▦</span>
                    Microsoft
                </button>
            </div>

            <!-- ================= REGISTER ================= -->
            <div class="border-t border-slate-100 pt-6 text-center">
                <span class="text-sm text-slate-500">ليس لديك حساب؟</span>
                <a class="mr-1 text-sm font-bold text-emerald-600 transition hover:text-emerald-800" href="{{ route('register') }}">إنشاء حساب جديد</a>
            </div>
        </form>
    </div>
</x-guest-layout>