<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4 rounded-xl" :status="session('status')" />

    <div class="mb-6">
        <h2 class="text-2xl font-black text-gray-900">Masuk ke Sistem</h2>
        <p class="text-gray-500 text-sm mt-1">Masukkan email dan kata sandi Anda</p>
        <div class="mt-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-green-50 text-green-700 text-[10px] font-bold uppercase tracking-wider border border-green-100">
            <i class="fas fa-shield-alt"></i> Koneksi Aman & Terenkripsi
        </div>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Alamat Email</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400">
                    <i class="fas fa-envelope text-sm"></i>
                </span>
                <input id="email" type="email" name="email" value="{{ old('email') }}" 
                       class="w-full pl-10 border-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 text-sm py-2.5" 
                       required autofocus autocomplete="username" placeholder="bendahara@mts.sch.id">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Kata Sandi</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400">
                    <i class="fas fa-lock text-sm"></i>
                </span>
                <input id="password" type="password" name="password" 
                       class="w-full pl-10 border-gray-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 text-sm py-2.5" 
                       required autocomplete="current-password" placeholder="••••••••">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox" 
                       class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500" name="remember">
                <span class="text-sm text-gray-600">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-emerald-600 hover:text-emerald-700 font-medium" href="{{ route('password.request') }}">
                    Lupa sandi?
                </a>
            @endif
        </div>

        <button type="submit" 
                class="w-full py-3 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 active:scale-[0.99] transition shadow-sm text-sm">
            <i class="fas fa-sign-in-alt mr-2"></i> Masuk ke Sistem
        </button>
    </form>

    <p class="mt-6 text-center text-xs text-gray-400">
        Sistem Keuangan MTs Miftahul Ulum Dampit &copy; {{ date('Y') }}
    </p>
</x-guest-layout>
