@php
    $configuracion = $configuracion ?? \App\Models\Configuracion::first();
@endphp

<x-guest-layout>

    @if (session('status'))
        <div class="sx-auth-status">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="sx-auth-error-summary">
            <strong>No fue posible iniciar sesión.</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="sx-auth-form">
        @csrf

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
                autofocus
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
                    autocomplete="current-password"
                    placeholder="••••••••">

                <button
                    type="button"
                    class="sx-auth-password-toggle"
                    data-target="password"
                    aria-label="Mostrar contraseña">
                    <i class="bi bi-eye"></i>
                </button>
            </div>

            @error('password')
                <p class="sx-auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="sx-auth-form-row">

            <label for="remember_me" class="sx-auth-check">
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    value="1">

                <span>Recordarme</span>
            </label>

            @if (Route::has('password.request'))
                <a
                    class="sx-auth-link"
                    href="{{ route('password.request') }}">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif

        </div>

        <button type="submit" class="sx-auth-submit">
            <i class="bi bi-box-arrow-in-right"></i>
            Iniciar sesión
        </button>

    </form>

    <div class="sx-auth-divider">
        <span>o continúa con</span>
    </div>

    <a href="{{ route('google.redirect') }}" class="sx-auth-google">
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <path fill="#4285F4" d="M21.35 12.23c0-.79-.07-1.56-.22-2.3H12v4.35h5.23a4.46 4.46 0 0 1-1.94 2.93v2.43h3.14c1.84-1.69 2.92-4.18 2.92-7.41z"/>
            <path fill="#34A853" d="M12 21.99c2.63 0 4.84-.87 6.45-2.35l-3.14-2.43c-.87.58-1.98.92-3.31.92-2.54 0-4.69-1.72-5.46-4.03H3.3v2.5A9.75 9.75 0 0 0 12 21.99z"/>
            <path fill="#FBBC05" d="M6.54 14.1a5.86 5.86 0 0 1 0-3.73v-2.5H3.3a9.75 9.75 0 0 0 0 8.73l3.24-2.5z"/>
            <path fill="#EA4335" d="M12 6.34c1.43 0 2.71.49 3.72 1.45l2.79-2.79C16.84 3.38 14.63 2.01 12 2.01a9.75 9.75 0 0 0-8.7 5.86l3.24 2.5C7.31 8.06 9.46 6.34 12 6.34z"/>
        </svg>

        Continuar con Google
    </a>

    <div class="sx-auth-register-note">
        ¿Todavía no tienes una cuenta?
        <a class="sx-auth-link" href="{{ route('register') }}">
            Regístrate en {{ $configuracion->nombre_restaurante ?? 'Sabor Express' }}
        </a>
    </div>

</x-guest-layout>
