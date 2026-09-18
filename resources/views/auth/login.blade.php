<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                type="password"
                name="password"
                required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                {{ __('Forgot your password?') }}
            </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    {{-- Login con Google --}}
    <div class="mt-4">
        <a href="{{ route('google.redirect') }}"
            class="flex items-center justify-center w-full px-4 py-2 border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50 transition">

            <svg class="w-5 h-5 me-2"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path fill="#4285F4"
                    d="M21.35 12.23c0-.79-.07-1.56-.22-2.3H12v4.35h5.23a4.46 4.46 0 0 1-1.94 2.93v2.43h3.14c1.84-1.69 2.92-4.18 2.92-7.41z" />
                <path fill="#34A853"
                    d="M12 21.99c2.63 0 4.84-.87 6.45-2.35l-3.14-2.43c-.87.58-1.98.92-3.31.92-2.54 0-4.69-1.72-5.46-4.03H3.3v2.5A9.75 9.75 0 0 0 12 21.99z" />
                <path fill="#FBBC05"
                    d="M6.54 14.1a5.86 5.86 0 0 1 0-3.73v-2.5H3.3a9.75 9.75 0 0 0 0 8.73l3.24-2.5z" />
                <path fill="#EA4335"
                    d="M12 6.34c1.43 0 2.71.49 3.72 1.45l2.79-2.79C16.84 3.38 14.63 2.01 12 2.01a9.75 9.75 0 0 0-8.7 5.86l3.24 2.5C7.31 8.06 9.46 6.34 12 6.34z" />
            </svg>

            Continuar con Google
        </a>
    </div>
</x-guest-layout>