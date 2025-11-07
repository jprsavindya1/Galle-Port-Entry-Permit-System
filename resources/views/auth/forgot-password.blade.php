<x-guest-layout>
    <!-- Header -->
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ __('Reset Password') }}</h2>
        <p class="text-sm text-gray-600 leading-relaxed">
            {{ __('Enter your email address and we\'ll send you a link to reset your password.') }}
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-6">
            <x-input-label for="email" :value="__('Email Address')" class="text-gray-700 font-semibold" />
            <x-text-input id="email" class="block mt-2 w-full px-4 py-3 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" type="email" name="email" :value="old('email')" placeholder="Enter your email address" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Buttons -->
        <div class="flex items-center gap-3">
            <!-- Back to Login Button -->
            <a href="{{ route('login') }}" 
               class="flex-1 inline-flex items-center justify-center px-5 py-3 bg-gray-100 border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 tracking-wide shadow-sm hover:bg-gray-200 hover:border-gray-400 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                {{ __('Back') }}
            </a>
            
            <!-- Send Reset Link Button -->
            <button type="submit"
                    style="background: linear-gradient(135deg, #002B5C 0%, #003d7a 100%);"
                    class="flex-1 inline-flex items-center justify-center px-5 py-3 border border-transparent rounded-lg font-bold text-sm text-white tracking-wide shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200"
                    onmouseover="this.style.background='linear-gradient(135deg, #003d7a 0%, #004d8f 100%)'"
                    onmouseout="this.style.background='linear-gradient(135deg, #002B5C 0%, #003d7a 100%)'">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
                {{ __('Send Reset Link') }}
            </button>
        </div>
    </form>
</x-guest-layout>
