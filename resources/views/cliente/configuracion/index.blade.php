@extends('layouts.cliente')

@section('title', 'Mi perfil')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <span class="text-uppercase small fw-semibold text-muted">Sabor Express</span>
        <h1 class="h2 mb-1">
            <i class="bi bi-person-gear me-2"></i>
            Configuración de mi perfil
        </h1>
        <p class="text-muted mb-0">
            Actualiza tus datos personales y tu contraseña.
        </p>
    </div>

    @if(session('profile_status'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill me-1"></i>
            {{ session('profile_status') }}
        </div>
    @endif

    @if(session('password_status'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill me-1"></i>
            {{ session('password_status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Revisa los datos ingresados.</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="fs-2 text-primary">
                            <i class="bi bi-person-circle"></i>
                        </div>
                        <div>
                            <h2 class="h4 mb-1">Datos personales</h2>
                            <p class="text-muted mb-0">Modifica tu nombre y teléfono.</p>
                        </div>
                    </div>

                    <form action="{{ route('cliente.configuracion.update') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nombre completo</label>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                class="form-control"
                                value="{{ old('name', $user->name) }}"
                                maxlength="255"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="telefono" class="form-label fw-semibold">Número de teléfono</label>
                            <input
                                id="telefono"
                                name="telefono"
                                type="text"
                                class="form-control"
                                value="{{ old('telefono', $user->telefono) }}"
                                maxlength="20"
                                placeholder="Ej. 77777777">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Correo electrónico</label>
                            <input
                                type="email"
                                class="form-control"
                                value="{{ $user->email }}"
                                disabled>
                            <div class="form-text">
                                El correo asociado a tu cuenta no se modifica desde este apartado.
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>
                            Guardar datos
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="fs-2 text-warning">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <div>
                            <h2 class="h4 mb-1">Cambiar contraseña</h2>
                            <p class="text-muted mb-0">Usa tu contraseña actual para definir una nueva.</p>
                        </div>
                    </div>

                    <form action="{{ route('cliente.configuracion.password') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="current_password" class="form-label fw-semibold">Contraseña actual</label>
                            <input
                                id="current_password"
                                name="current_password"
                                type="password"
                                class="form-control @error('current_password', 'passwordUpdate') is-invalid @enderror"
                                autocomplete="current-password"
                                required>
                            @error('current_password', 'passwordUpdate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Nueva contraseña</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                class="form-control @error('password', 'passwordUpdate') is-invalid @enderror"
                                autocomplete="new-password"
                                minlength="8"
                                required>
                            @error('password', 'passwordUpdate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirmar nueva contraseña</label>
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                class="form-control"
                                autocomplete="new-password"
                                minlength="8"
                                required>
                        </div>

                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-key-fill me-1"></i>
                            Actualizar contraseña
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
