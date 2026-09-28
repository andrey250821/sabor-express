@extends('layouts.cocinero')

@section('title', 'Mi perfil')
@section('section', 'Cuenta')
@section('heading', 'Mi perfil')

@section('content')
<div class="container-fluid py-4">

    <div class="mb-4">
        <span class="text-uppercase small fw-semibold text-muted">
            Sabor Express
        </span>

        <h1 class="h2 mb-1">
            <i class="bi bi-person-circle me-2"></i>
            Información del cocinero
        </h1>

        <p class="text-muted mb-0">
            Consulta y actualiza la información de tu perfil.
        </p>
    </div>

    @if(session('profile_status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i>
            {{ session('profile_status') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Cerrar"></button>
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

        <div class="col-12 col-xl-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div
                            style="
                                width: 72px;
                                height: 72px;
                                border-radius: 50%;
                                overflow: hidden;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                background: linear-gradient(135deg, #252a33, #171a20);
                                color: #ff80ab;
                                font-size: 28px;
                                font-weight: 700;
                                flex: 0 0 72px;
                            ">

                            @if($user->foto_perfil_url)
                                <img
                                    src="{{ $user->foto_perfil_url }}"
                                    alt="Foto de {{ $user->name }}"
                                    style="width:100%;height:100%;object-fit:cover;display:block;">
                            @else
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            @endif

                        </div>

                        <div>
                            <h2 class="h4 mb-1">
                                Datos personales
                            </h2>

                            <p class="text-muted mb-0">
                                Estos datos se muestran en tu panel de cocina.
                            </p>
                        </div>
                    </div>

                    <form
                        action="{{ route('cocinero.perfil.update') }}"
                        method="POST"
                        enctype="multipart/form-data">

                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label
                                for="name"
                                class="form-label fw-semibold">
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
                            <label
                                for="telefono"
                                class="form-label fw-semibold">
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
                            <label
                                for="foto_perfil"
                                class="form-label fw-semibold">
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
                            class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>
                            Guardar cambios
                        </button>

                    </form>

                </div>
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="fs-2 text-warning">
                            <i class="bi bi-person-vcard-fill"></i>
                        </div>

                        <div>
                            <h2 class="h4 mb-1">
                                Información de la cuenta
                            </h2>

                            <p class="text-muted mb-0">
                                Datos administrados por Sabor Express.
                            </p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">
                            Correo electrónico
                        </small>

                        <strong>
                            {{ $user->email }}
                        </strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">
                            Rol
                        </small>

                        <strong>
                            {{ $user->role?->nombre ?? 'Cocinero' }}
                        </strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">
                            Estado
                        </small>

                        @if($user->estado === 'activo')
                            <span class="badge text-bg-success">
                                Activo
                            </span>
                        @else
                            <span class="badge text-bg-secondary">
                                Inactivo
                            </span>
                        @endif
                    </div>

                    <div>
                        <small class="text-muted d-block">
                            Miembro desde
                        </small>

                        <strong>
                            {{ $user->created_at?->format('d/m/Y') ?? 'No disponible' }}
                        </strong>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
