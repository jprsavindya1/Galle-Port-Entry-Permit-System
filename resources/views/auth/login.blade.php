<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-6">
        <div class="w-16 h-16 bg-blue-600/10 border border-blue-400/20 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-500 shadow-[0_0_20px_rgba(59,130,246,0.15)] relative">
            <div class="absolute inset-0 rounded-full border border-blue-400/15 animate-ping opacity-30"></div>
            <i class="bi bi-shield-fill-check text-3xl text-[#13314C]"></i>
        </div>
        <h3 class="text-2xl font-bold text-[#13314C] tracking-tight">Sign in to your account</h3>
        <p class="text-sm text-[#183650]/80 mt-1">Access your permit dashboard</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="w-full">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
            <div class="flex items-center justify-between mb-2">
                <label for="email" class="text-sm font-semibold text-[#13314C]">{{ __('Email Address') }}</label>
                <span class="text-xs text-[#1FB5C7] font-semibold flex items-center gap-1">
                    <i class="bi bi-check2 text-[#1FB5C7] font-bold"></i> User ID verified
                </span>
            </div>
            <div class="relative rounded-lg shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#13314C]/65">
                    <i class="bi bi-envelope text-lg"></i>
                </div>
                <input id="email" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       autocomplete="username"
                       placeholder="superadmin@slpa.lk"
                       class="block w-full pl-11 pr-4 py-3 border border-[#13314C]/20 rounded-xl text-[#13314C] bg-white/45 placeholder-[#13314C]/40 focus:outline-none focus:ring-2 focus:ring-[#1FB5C7]/30 focus:border-[#1FB5C7]/40 transition-all duration-300 shadow-inner text-[0.95rem]" 
                       style="height: 48px;" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div class="mb-4">
            <div class="mb-2">
                <label for="password" class="text-sm font-semibold text-[#13314C]">{{ __('Password') }}</label>
            </div>
            <div class="relative rounded-lg shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#13314C]/65">
                    <i class="bi bi-lock text-lg"></i>
                </div>
                <input id="password" 
                       type="password" 
                       name="password" 
                       required 
                       autocomplete="current-password"
                       placeholder="••••••••"
                       class="block w-full pl-11 pr-11 py-3 border border-[#13314C]/20 rounded-xl text-[#13314C] bg-white/45 placeholder-[#13314C]/40 focus:outline-none focus:ring-2 focus:ring-[#1FB5C7]/30 focus:border-[#1FB5C7]/40 transition-all duration-300 shadow-inner text-[0.95rem]" 
                       style="height: 48px;" />
                
                <!-- Eye Icon Toggle -->
                <button type="button" 
                        onclick="togglePassword()" 
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#13314C]/60 hover:text-[#13314C] transition focus:outline-none"
                        style="height: 48px; z-index: 10;"
                        tabindex="-1">
                    <i id="eye-icon" class="bi bi-eye text-lg"></i>
                    <i id="eye-slash-icon" class="bi bi-eye-slash text-lg hidden"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between mb-6 mt-3">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" 
                       type="checkbox" 
                       class="rounded border-[#13314C]/25 text-[#13314C] focus:ring-[#13314C]/20 bg-white/45 transition duration-200" 
                       name="remember">
                <span class="ms-2 text-sm text-[#183650]/80 group-hover:text-[#13314C] transition duration-200">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-[#1278B9] hover:text-[#13314C] font-semibold focus:outline-none transition-colors duration-200" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <!-- Login Button -->
        <div>
            <button type="submit"
                    class="w-full inline-flex items-center justify-center px-5 py-3 border border-transparent rounded-xl font-bold text-base text-white tracking-wide shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-[#1FB5C7]/50 focus:ring-offset-2 transition-all duration-300 hover:-translate-y-0.5 group"
                    style="background: #13314C;">
                <span>{{ __('Sign In') }}</span>
                <i class="bi bi-arrow-right ms-2 text-base transition-transform duration-300 group-hover:translate-x-1"></i>
            </button>
        </div>
    </form>

    <!-- Password Toggle Script -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeSlashIcon = document.getElementById('eye-slash-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        }
    </script>
</x-guest-layout>
