@extends('layouts.cocinero')

@section('title', 'Mi perfil')
@section('section', 'Cuenta')
@section('heading', 'Mi perfil')

@section('content')

<div class="container-fluid cocinero-profile-page">

    <div class="cocinero-profile-intro">
        <small>SABOR EXPRESS · CUENTA</small>
        <h2>
            <i class="bi bi-person-circle me-2"></i>
            Información del cocinero
        </h2>
        <p>
            Actualiza tus datos personales. El correo, rol y acceso son administrados por Sabor Express.
        </p>
    </div>


    @if(session('profile_status'))
        <div class="alert alert-success alert-dismissible fade show cocinero-alert mb-4" role="alert">
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
        <div class="alert alert-danger alert-dismissible fade show cocinero-alert mb-4" role="alert">

            <strong>
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
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

            <section class="cocinero-profile-card">

                <div class="cocinero-profile-card-head">
                    <i class="bi bi-pencil-square fs-5"></i>

                    <div>
                        <h3>Datos personales</h3>
                        <p>Información que puedes modificar desde tu cuenta.</p>
                    </div>
                </div>

                <div class="cocinero-profile-card-body">

                    <div class="d-flex align-items-center gap-3 mb-4">

                        <div class="cocinero-profile-preview" data-photo-preview>

                            @if($user->foto_perfil_url)
                                <img
                                    src="{{ $user->foto_perfil_url }}"
                                    alt="Foto de {{ $user->name }}">
                            @else
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            @endif

                        </div>

                        <div>
                            <div class="text-white fw-bold">
                                {{ $user->name }}
                            </div>

                            <div class="text-secondary small">
                                Foto visible en tu panel de cocina
                            </div>
                        </div>

                    </div>


                    <form
                        action="{{ route('cocinero.perfil.update') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        data-loading>

                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Nombre completo
                            </label>

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
                            <label for="telefono" class="form-label">
                                Número de teléfono
                            </label>

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
                            <label for="foto_perfil" class="form-label">
                                Foto de perfil
                            </label>

                            <input
                                id="foto_perfil"
                                name="foto_perfil"
                                type="file"
                                class="form-control @error('foto_perfil') is-invalid @enderror"
                                accept="image/jpeg,image/png,image/webp">

                            <div class="form-text">
                                JPG, PNG o WEBP. Máximo 2 MB.
                            </div>

                            @error('foto_perfil')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>


                        <button
                            type="submit"
                            class="btn cocinero-profile-save">
                            <i class="bi bi-save me-1"></i>
                            Guardar cambios
                        </button>

                    </form>

                </div>

            </section>

        </div>


        {{-- INFORMACIÓN DE CUENTA --}}
        <div class="col-12 col-xl-5">

            <section class="cocinero-profile-card">

                <div class="cocinero-profile-card-head">
                    <i class="bi bi-person-vcard fs-5"></i>

                    <div>
                        <h3>Información de la cuenta</h3>
                        <p>Datos administrados por Sabor Express.</p>
                    </div>
                </div>


                <div class="cocinero-profile-card-body">

                    <div class="cocinero-account-row">
                        <small>Correo electrónico</small>
                        <strong class="text-break">{{ $user->email }}</strong>
                    </div>

                    <div class="cocinero-account-row">
                        <small>Rol</small>
                        <strong>{{ $user->role?->nombre ?? 'Cocinero' }}</strong>
                    </div>

                    <div class="cocinero-account-row">
                        <small>Estado de trabajo</small>

                        @if($user->estado === 'activo')
                            <span class="cocinero-account-status activo">
                                <i class="bi bi-circle-fill"></i>
                                Activo
                            </span>
                        @else
                            <span class="cocinero-account-status inactivo">
                                <i class="bi bi-circle-fill"></i>
                                Inactivo
                            </span>
                        @endif
                    </div>

                    <div class="cocinero-account-row">
                        <small>Miembro desde</small>
                        <strong>
                            {{ $user->created_at?->format('d/m/Y') ?? 'No disponible' }}
                        </strong>
                    </div>

                    <div class="mt-3 p-3 rounded-3 bg-black bg-opacity-25 border border-secondary border-opacity-25">
                        <div class="small text-secondary">
                            <i class="bi bi-info-circle me-1 text-warning"></i>
                            Tu estado de trabajo se cambia desde el panel lateral.
                        </div>
                    </div>

                </div>

            </section>

        </div>

    </div>

</div>

@endsection
