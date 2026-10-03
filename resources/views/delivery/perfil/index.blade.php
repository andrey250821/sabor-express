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
                El correo, rol y control de disponibilidad pertenecen a la cuenta administrada por el restaurante.
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


    <div class="row g-4">

        <div class="col-12 col-xl-7">

            <section
                class="delivery-profile-card"
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

                            <div class="delivery-file-wrapper">

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


        <div class="col-12 col-xl-5">

            <aside
                class="delivery-profile-card delivery-profile-account-card"
                data-delivery-animate>

                <div class="delivery-profile-card-header">

                    <div class="delivery-profile-card-heading">

                        <div class="delivery-profile-heading-icon">
                            <i class="bi bi-person-vcard-fill"></i>
                        </div>

                        <div>

                            <span>
                                CUENTA
                            </span>

                            <h2>
                                Información de acceso
                            </h2>

                        </div>

                    </div>

                </div>


                <div class="delivery-account-list">

                    <div class="delivery-account-item">

                        <span>
                            <i class="bi bi-envelope"></i>
                            Correo electrónico
                        </span>

                        <strong>
                            {{ $user->email }}
                        </strong>

                        <small>
                            El correo no se modifica desde este apartado.
                        </small>

                    </div>


                    <div class="delivery-account-item">

                        <span>
                            <i class="bi bi-shield-check"></i>
                            Rol
                        </span>

                        <strong>
                            {{ $user->role?->nombre ?? 'Delivery' }}
                        </strong>

                    </div>


                    <div class="delivery-account-item">

                        <span>
                            <i class="bi bi-activity"></i>
                            Disponibilidad
                        </span>

                        <strong>
                            {{ $user->estado === 'activo'
                                ? 'Puedes recibir nuevas asignaciones'
                                : 'No recibirás nuevas asignaciones' }}
                        </strong>

                    </div>


                    <div class="delivery-account-item">

                        <span>
                            <i class="bi bi-calendar3"></i>
                            Miembro desde
                        </span>

                        <strong>
                            {{ $user->created_at?->format('d/m/Y') ?? 'No disponible' }}
                        </strong>

                    </div>

                </div>


                <div class="delivery-profile-note">

                    <i class="bi bi-info-circle-fill"></i>

                    <span>
                        Para activar o desactivar tu disponibilidad utiliza el control
                        de estado ubicado en el menú lateral.
                    </span>

                </div>

            </aside>

        </div>

    </div>

</div>

@endsection
