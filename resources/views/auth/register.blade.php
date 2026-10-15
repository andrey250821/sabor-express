@php
    $configuracion = $configuracion ?? \App\Models\Configuracion::first();
@endphp

<x-guest-layout>

    @if ($errors->any())
        <div class="sx-auth-error-summary">
            <strong>Revisa los datos del registro.</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="sx-auth-form">
        @csrf

        <div class="sx-auth-field">
            <label for="name" class="sx-auth-label">
                Nombre completo
            </label>

            <input
                id="name"
                name="name"
                type="text"
                class="sx-auth-input"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                placeholder="Tu nombre">

            @error('name')
                <p class="sx-auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="sx-auth-field">
            <label for="telefono" class="sx-auth-label">
                Número de teléfono
            </label>

            <input
                id="telefono"
                name="telefono"
                type="tel"
                class="sx-auth-input"
                value="{{ old('telefono') }}"
                required
                autocomplete="tel"
                placeholder="Ej. 7 1234567">

            @error('telefono')
                <p class="sx-auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="sx-auth-field">
            <label for="email" class="sx-auth-label">
                Correo electrónico
            </label>

            <input
                id="email"
                name="email"
                type="email"
                class="sx-auth-input"
                value="{{ old('email') }}"
                required
                autocomplete="username"
                placeholder="tucorreo@gmail.com">

            @error('email')
                <p class="sx-auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="sx-auth-field">
            <label for="password" class="sx-auth-label">
                Contraseña
            </label>

            <div class="sx-auth-input-wrap">
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="sx-auth-input has-toggle"
                    required
                    autocomplete="new-password"
                    placeholder="Mínimo 8 caracteres">

                <button
                    type="button"
                    class="sx-auth-password-toggle"
                    data-target="password"
                    aria-label="Mostrar contraseña">
                    <i class="bi bi-eye"></i>
                </button>
            </div>

            <div id="sx-password-strength" class="sx-password-strength" data-level="0">
                <span></span>
            </div>

            @error('password')
                <p class="sx-auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="sx-auth-field">
            <label for="password_confirmation" class="sx-auth-label">
                Confirmar contraseña
            </label>

            <div class="sx-auth-input-wrap">
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    class="sx-auth-input has-toggle"
                    required
                    autocomplete="new-password"
                    placeholder="Repite tu contraseña">

                <button
                    type="button"
                    class="sx-auth-password-toggle"
                    data-target="password_confirmation"
                    aria-label="Mostrar contraseña">
                    <i class="bi bi-eye"></i>
                </button>
            </div>

            @error('password_confirmation')
                <p class="sx-auth-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="sx-auth-submit">
            <i class="bi bi-person-plus-fill"></i>
            Crear mi cuenta
        </button>

    </form>

    <div class="sx-auth-divider">
        <span>o regístrate con</span>
    </div>

    <a href="{{ route('google.redirect') }}" class="sx-auth-google">
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <path fill="#4285F4" d="M21.35 12.23c0-.79-.07-1.55-.23-2.23H12v4.22h5.24c-.23 1.35-1.02 2.5-2.17 3.27v2.72h3.51c2.06-1.9 3.24-4.7 3.24-7.98z"/>
            <path fill="#34A853" d="M12 21.5c2.95 0 5.43-.98 7.24-2.64l-3.51-2.72c-.98.66-2.23 1.06-3.73 1.06-2.86 0-5.28-1.93-6.15-4.52H2.22v2.8A10.94 10.94 0 0 0 12 21.5z"/>
            <path fill="#FBBC05" d="M5.85 12.68A6.58 6.58 0 0 1 5.5 10.5c0-.76.13-1.5.35-2.18v-2.8H2.22A10.94 10.94 0 0 0 1.5 10.5c0 1.76.42 3.42 1.22 4.98l3.13-2.8z"/>
            <path fill="#EA4335" d="M12 4.3c1.61 0 3.06.55 4.2 1.64l3.14-3.14C17.43 1.1 14.95 0 12 0 7.73 0 4.04 2.45 2.22 5.52l3.63 2.8C6.57 6.23 9.02 4.3 12 4.3z"/>
        </svg>

        Registrarme con Google
    </a>

    <div class="sx-auth-register-note">
        ¿Ya tienes una cuenta?
        <a class="sx-auth-link" href="{{ route('login') }}">
            Inicia sesión
        </a>
    </div>

</x-guest-layout>
