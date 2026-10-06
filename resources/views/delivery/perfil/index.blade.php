@extends('layouts.delivery')

@section('title', 'Mi perfil')
@section('section', 'Cuenta')
@section('heading', 'Mi perfil')

@section('content')

<div class="container-fluid delivery-profile-page">

    <header class="delivery-profile-header" data-delivery-animate>

        <div>

            <span class="delivery-section-label">
                CUENTA DEL DELIVERY
            </span>

            <h1>
                <i class="bi bi-person-circle"></i>
                Mi perfil
            </h1>

            <p>
                Consulta y actualiza tu información personal.
            </p>

        </div>

        <div class="delivery-profile-status-chip {{ $user->estado === 'activo' ? 'active' : 'inactive' }}">

            <span></span>

            {{ $user->estado === 'activo' ? 'Activo' : 'Inactivo' }}

        </div>

    </header>


    @if(session('profile_status'))

    <div
        class="delivery-profile-alert success"
        data-delivery-animate>

        <i class="bi bi-check-circle-fill"></i>

        <span>
            {{ session('profile_status') }}
        </span>

    </div>

    @endif


    @if($errors->any())

    <div
        class="delivery-profile-alert error"
        data-delivery-animate>

        <i class="bi bi-exclamation-triangle-fill"></i>

        <div>
            <strong>Revisa los datos ingresados.</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

    </div>

    @endif


    <div class="row g-4 align-items-stretch">

        <div class="col-12 col-xl-6 d-flex">

            <section
                class="delivery-profile-card w-100"
                data-delivery-animate>

                <div class="delivery-profile-card-header">

                    <div class="delivery-profile-identity">

                        <div class="delivery-profile-avatar">

                            @if($user->foto_perfil_url)

                            <img
                                src="{{ $user->foto_perfil_url }}"
                                alt="Foto de {{ $user->name }}">

                            @else

                            {{ strtoupper(substr($user->name, 0, 1)) }}

                            @endif

                        </div>

                        <div>

                            <span>
                                PERFIL PERSONAL
                            </span>

                            <h2>
                                Datos personales
                            </h2>

                            <p>
                                Estos datos se muestran en tu panel de Delivery.
                            </p>

                        </div>

                    </div>

                </div>


                <form
                    action="{{ route('delivery.perfil.update') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="delivery-profile-form">

                    @csrf
                    @method('PATCH')

                    <div class="row g-3">

                        <div class="col-12">

                            <label
                                for="name"
                                class="delivery-form-label">

                                Nombre completo

                            </label>

                            <div class="delivery-input-group">

                                <i class="bi bi-person"></i>

                                <input
                                    id="name"
                                    name="name"
                                    type="text"
                                    value="{{ old('name', $user->name) }}"
                                    maxlength="255"
                                    required>

                            </div>

                        </div>


                        <div class="col-12">

                            <label
                                for="telefono"
                                class="delivery-form-label">

                                Número de teléfono

                            </label>

                            <div class="delivery-input-group">

                                <i class="bi bi-telephone"></i>

                                <input
                                    id="telefono"
                                    name="telefono"
                                    type="text"
                                    value="{{ old('telefono', $user->telefono) }}"
                                    maxlength="20"
                                    placeholder="Ej. 77777777">

                            </div>

                        </div>


                        <div class="col-12">

                            <label
                                for="foto_perfil"
                                class="delivery-form-label">

                                Foto de perfil

                            </label>

                            <div class="delivery-file-wrapper delivery-interactive-field">

                                <i class="bi bi-camera-fill"></i>

                                <input
                                    id="foto_perfil"
                                    name="foto_perfil"
                                    type="file"
                                    class="@error('foto_perfil') is-invalid @enderror"
                                    accept="image/jpeg,image/png,image/webp">

                            </div>

                            <small class="delivery-form-help">
                                JPG, PNG o WEBP. Máximo 2 MB.
                            </small>

                            @error('foto_perfil')

                            <div class="delivery-field-error">
                                {{ $message }}
                            </div>

                            @enderror

                        </div>

                    </div>


                    <div class="delivery-profile-form-footer">

                        <span>
                            <i class="bi bi-shield-check"></i>
                            Tus datos están protegidos.
                        </span>

                        <button
                            type="submit"
                            class="delivery-profile-save"
                            data-delivery-interactive>

                            <i class="bi bi-check2-circle"></i>
                            Guardar cambios

                        </button>

                    </div>

                </form>

            </section>

        </div>

        <div class="col-12 col-xl-6 d-flex">

            {{-- CAMBIO DE CONTRASEÑA --}}
            <section class="delivery-password-card w-100" data-delivery-animate>

                <div class="delivery-password-header">

                    <div class="delivery-password-icon">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>

                    <div>
                        <span>SEGURIDAD DE LA CUENTA</span>
                        <h2>Cambiar contraseña</h2>
                        <p>
                            Usa tu contraseña actual para establecer una nueva contraseña de acceso.
                        </p>
                    </div>

                </div>

                @if(session('password_status'))

                <div class="delivery-password-alert success">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('password_status') }}</span>
                </div>

                @endif

                @if($errors->passwordUpdate->any())

                <div class="delivery-password-alert error">

                    <i class="bi bi-exclamation-triangle-fill"></i>

                    <div>
                        <strong>No se pudo actualizar la contraseña.</strong>
                        <ul>
                            @foreach($errors->passwordUpdate->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>

                </div>

                @endif

                <form
                    action="{{ route('delivery.perfil.password') }}"
                    method="POST"
                    class="delivery-password-form">

                    @csrf
                    @method('PATCH')

                    <div class="row g-3">

                        <div class="col-12 col-lg-4">

                            <label for="current_password" class="delivery-form-label">
                                Contraseña actual
                            </label>

                            <div class="delivery-password-input">

                                <i class="bi bi-lock"></i>

                                <input
                                    id="current_password"
                                    name="current_password"
                                    type="password"
                                    class="@error('current_password', 'passwordUpdate') is-invalid @enderror"
                                    autocomplete="current-password"
                                    required>

                                <button
                                    type="button"
                                    class="delivery-password-toggle" data-delivery-interactive
                                    data-password-toggle="current_password"
                                    aria-label="Mostrar contraseña actual">
                                    <i class="bi bi-eye"></i>
                                </button>

                            </div>

                            @error('current_password', 'passwordUpdate')
                                <small class="delivery-field-error">{{ $message }}</small>
                            @enderror

                        </div>

                        <div class="col-12 col-lg-4">

                            <label for="password" class="delivery-form-label">
                                Nueva contraseña
                            </label>

                            <div class="delivery-password-input">

                                <i class="bi bi-key-fill"></i>

                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    class="@error('password', 'passwordUpdate') is-invalid @enderror"
                                    autocomplete="new-password"
                                    minlength="8"
                                    required>

                                <button
                                    type="button"
                                    class="delivery-password-toggle" data-delivery-interactive
                                    data-password-toggle="password"
                                    aria-label="Mostrar nueva contraseña">
                                    <i class="bi bi-eye"></i>
                                </button>

                            </div>

                            @error('password', 'passwordUpdate')
                                <small class="delivery-field-error">{{ $message }}</small>
                            @enderror

                            <small class="delivery-form-help">
                                Mínimo 8 caracteres.
                            </small>

                        </div>

                        <div class="col-12 col-lg-4">

                            <label for="password_confirmation" class="delivery-form-label">
                                Confirmar nueva contraseña
                            </label>

                            <div class="delivery-password-input">

                                <i class="bi bi-check2-circle"></i>

                                <input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    autocomplete="new-password"
                                    minlength="8"
                                    required>

                                <button
                                    type="button"
                                    class="delivery-password-toggle" data-delivery-interactive
                                    data-password-toggle="password_confirmation"
                                    aria-label="Mostrar confirmación de contraseña">
                                    <i class="bi bi-eye"></i>
                                </button>

                            </div>

                        </div>

                    </div>

                    <div class="delivery-password-footer">

                        <div class="delivery-password-tip">
                            <i class="bi bi-info-circle-fill"></i>
                            <span>
                                No compartas tu contraseña y evita utilizar datos fáciles de adivinar.
                            </span>
                        </div>

                        <button
                            type="submit"
                            class="delivery-password-save"
                            data-delivery-interactive>

                            <i class="bi bi-key-fill"></i>
                            Actualizar contraseña

                        </button>

                    </div>

                </form>

            </section>

        </div>

    </div>

</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = document.getElementById(
                button.getAttribute('data-password-toggle')
            );

            if (!input) {
                return;
            }

            const icon = button.querySelector('i');
            const showing = input.type === 'text';

            input.type = showing ? 'password' : 'text';

            if (icon) {
                icon.classList.toggle('bi-eye', showing);
                icon.classList.toggle('bi-eye-slash', !showing);
            }

            button.setAttribute(
                'aria-label',
                showing ? 'Mostrar contraseña' : 'Ocultar contraseña'
            );
        });
    });
});
</script>
@endpush
@endsection
