<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                type="password"
                name="password"
                required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                type="password"
                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Separador -->
    <div class="flex items-center my-6">
        <div class="flex-1 border-t border-gray-300"></div>
        <span class="px-3 text-sm text-gray-500">o</span>
        <div class="flex-1 border-t border-gray-300"></div>
    </div>

    <!-- Registro con Google -->
    <div>
        <a href="{{ route('google.redirect') }}"
            class="flex items-center justify-center w-full px-4 py-2 border border-gray-300 rounded-md bg-white text-gray-700 hover:bg-gray-50 transition">

            <svg class="w-5 h-5 me-2"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path fill="#4285F4"
                    d="M21.35 12.23c0-.79-.07-1.55-.23-2.23H12v4.22h5.24c-.23 1.35-1.02 2.5-2.17 3.27v2.72h3.51c2.06-1.9 3.24-4.7 3.24-7.98z" />
                <path fill="#34A853"
                    d="M12 21.5c2.95 0 5.43-.98 7.24-2.64l-3.51-2.72c-.98.66-2.23 1.06-3.73 1.06-2.86 0-5.28-1.93-6.15-4.52H2.22v2.8A10.94 10.94 0 0 0 12 21.5z" />
                <path fill="#FBBC05"
                    d="M5.85 12.68A6.58 6.58 0 0 1 5.5 10.5c0-.76.13-1.5.35-2.18V5.52H2.22A10.94 10.94 0 0 0 1.5 10.5c0 1.76.42 3.42 1.22 4.98l3.13-2.8z" />
                <path fill="#EA4335"
                    d="M12 4.3c1.61 0 3.06.55 4.2 1.64l3.14-3.14C17.43 1.1 14.95 0 12 0 7.73 0 4.04 2.45 2.22 5.52l3.63 2.8C6.57 6.23 9.02 4.3 12 4.3z" />
            </svg>

            Registrarse con Google
        </a>
    </div>
</x-guest-layout>