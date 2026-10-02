@extends('layouts.cocinero')

@section('title', 'Mi perfil')
@section('section', 'Cuenta')
@section('heading', 'Mi perfil')

@section('content')

<div class="container-fluid px-0 cocinero-profile-page">

    {{-- CABECERA --}}
    <section class="cocinero-profile-hero mb-4 cocinero-reveal">

        <div class="p-4 p-lg-5">

            <div class="row align-items-center g-4">

                <div class="col-12 col-lg">

                    <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-3">

                        <div class="cocinero-profile-avatar">

                            @if($user->foto_perfil_url)

                                <img
                                    src="{{ $user->foto_perfil_url }}"
                                    alt="Foto de {{ $user->name }}">

                            @else

                                {{ strtoupper(substr($user->name, 0, 1)) }}

                            @endif

                        </div>

                        <div>
                            <span class="cocinero-profile-eyebrow">
                                Sabor Express · Cocina
                            </span>

                            <h2 class="cocinero-profile-title">
                                {{ $user->name }}
                            </h2>

                            <p class="cocinero-profile-subtitle">
                                Administra los datos personales que aparecen en tu panel de cocina.
                            </p>
                        </div>

                    </div>
                </div>

                <div class="col-12 col-lg-auto">

                    <span class="badge rounded-pill text-bg-success px-3 py-2">
                        <i class="bi bi-circle-fill me-1"></i>
                        {{ $user->estado === 'activo' ? 'Activo' : 'Inactivo' }}
                    </span>

                </div>
            </div>
        </div>
    </section>

    {{-- MENSAJES --}}
    @if(session('profile_status'))

        <div class="alert alert-success alert-dismissible fade show cocinero-alert mb-4"
            role="alert">

            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('profile_status') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Cerrar"></button>
        </div>

    @endif

    @if($errors->any())

        <div class="alert alert-danger alert-dismissible fade show cocinero-alert mb-4"
            role="alert">

            <strong>
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                Revisa los datos ingresados.
            </strong>

            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Cerrar"></button>
        </div>

    @endif

    <div class="row g-4">

        {{-- DATOS EDITABLES --}}
        <div class="col-12 col-xl-7">

            <section class="cocinero-detail-card h-100 cocinero-reveal">

                <div class="cocinero-detail-card-header">

                    <div>
                        <div class="cocinero-detail-card-icon">
                            <i class="bi bi-person-fill"></i>
                        </div>

                        <div>
                            <h3>Datos personales</h3>
                            <span>Información que puedes actualizar</span>
                        </div>
                    </div>
                </div>

                <div class="p-4">

                    <form
                        action="{{ route('cocinero.perfil.update') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        data-loading-text="Guardando...">

                        @csrf
                        @method('PATCH')

                        <div class="mb-3">

                            <label
                                for="name"
                                class="form-label cocinero-form-label">

                                Nombre completo
                            </label>

                            <input
                                id="name"
                                name="name"
                                type="text"
                                class="form-control cocinero-form-control"
                                value="{{ old('name', $user->name) }}"
                                maxlength="255"
                                autocomplete="name"
                                required>

                        </div>

                        <div class="mb-3">

                            <label
                                for="telefono"
                                class="form-label cocinero-form-label">

                                Número de teléfono
                            </label>

                            <input
                                id="telefono"
                                name="telefono"
                                type="text"
                                class="form-control cocinero-form-control"
                                value="{{ old('telefono', $user->telefono) }}"
                                maxlength="20"
                                autocomplete="tel"
                                placeholder="Ej. 77777777">

                        </div>

                        <div class="mb-4">

                            <label
                                for="foto_perfil"
                                class="form-label cocinero-form-label">

                                Foto de perfil
                            </label>

                            <input
                                id="foto_perfil"
                                name="foto_perfil"
                                type="file"
                                class="form-control cocinero-form-control @error('foto_perfil') is-invalid @enderror"
                                accept="image/jpeg,image/png,image/webp">

                            <div class="cocinero-file-note mt-2">
                                JPG, PNG o WEBP · máximo 2 MB.
                            </div>

                            @error('foto_perfil')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <button
                            type="submit"
                            class="btn cocinero-save-button w-100 js-ripple">

                            <i class="bi bi-save me-1"></i>
                            Guardar cambios
                        </button>

                    </form>
                </div>
            </section>
        </div>

        {{-- DATOS DE CUENTA --}}
        <div class="col-12 col-xl-5">

            <section class="cocinero-detail-card h-100 cocinero-reveal">

                <div class="cocinero-detail-card-header">

                    <div>
                        <div class="cocinero-detail-card-icon warning">
                            <i class="bi bi-person-vcard-fill"></i>
                        </div>

                        <div>
                            <h3>Información de la cuenta</h3>
                            <span>Datos administrados por Sabor Express</span>
                        </div>
                    </div>
                </div>

                <div class="p-4">

                    <div class="cocinero-account-item">

                        <small>Correo electrónico</small>

                        <strong>
                            {{ $user->email }}
                        </strong>
                    </div>

                    <div class="cocinero-account-item">

                        <small>Rol</small>

                        <strong>
                            {{ $user->role?->nombre ?? 'Cocinero' }}
                        </strong>
                    </div>

                    <div class="cocinero-account-item">

                        <small>Estado de trabajo</small>

                        <strong>
                            {{ $user->estado === 'activo'
                                ? 'Activo · puedes gestionar pedidos'
                                : 'Inactivo · no recibirás nuevos pedidos' }}
                        </strong>
                    </div>

                    <div class="cocinero-account-item">

                        <small>Miembro desde</small>

                        <strong>
                            {{ $user->created_at?->format('d/m/Y') ?? 'No disponible' }}
                        </strong>
                    </div>

                    <div class="alert alert-warning border-0 mt-4 mb-0 small">

                        <i class="bi bi-info-circle-fill me-1"></i>

                        El correo, el rol y el estado operativo están controlados
                        desde la administración del restaurante.

                    </div>
                </div>
            </section>
        </div>
    </div>

</div>

@endsection
