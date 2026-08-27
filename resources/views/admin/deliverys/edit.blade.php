@extends('layouts.admin')

@section('content')

<div class="delivery-form-page">

    <div class="delivery-form-card">


        {{-- HEADER --}}
        <div class="delivery-form-header edit">

            <div>

                <h3>

                    <i class="bi bi-pencil-square"></i>

                    Editar Repartidor

                </h3>

                <p>
                    Modifica la información del repartidor.
                </p>

            </div>


            <div class="delivery-form-icon edit">

                <i class="bi bi-bicycle"></i>

            </div>

        </div>


        {{-- ERRORES --}}
        @if($errors->any())

        <div class="alert delivery-form-error">

            <strong>
                Corrige los siguientes errores:
            </strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                <li>
                    {{ $error }}
                </li>

                @endforeach

            </ul>

        </div>

        @endif


        {{-- FORM --}}
        <div class="delivery-form-body">

            <form
                action="{{ route('admin.deliverys.update', $delivery->id) }}"
                method="POST">

                @csrf

                @method('PUT')


                <div class="row g-4">


                    {{-- ID --}}
                    <div class="col-md-6">

                        <label class="delivery-form-label">

                            <i class="bi bi-hash"></i>

                            ID

                        </label>


                        <input
                            type="text"
                            class="form-control delivery-input"
                            value="{{ $delivery->id }}"
                            disabled>

                    </div>


                    {{-- NOMBRE --}}
                    <div class="col-md-6">

                        <label class="delivery-form-label">

                            <i class="bi bi-person"></i>

                            Nombre completo

                        </label>


                        <input
                            type="text"
                            name="name"
                            class="form-control delivery-input"
                            value="{{ old('name', $delivery->name) }}"
                            required>

                    </div>


                    {{-- EMAIL --}}
                    <div class="col-md-6">

                        <label class="delivery-form-label">

                            <i class="bi bi-envelope"></i>

                            Correo electrónico

                        </label>


                        <input
                            type="email"
                            name="email"
                            class="form-control delivery-input"
                            value="{{ old('email', $delivery->email) }}"
                            required>

                    </div>


                    {{-- TELEFONO --}}
                    <div class="col-md-6">

                        <label class="delivery-form-label">

                            <i class="bi bi-telephone"></i>

                            Teléfono

                        </label>


                        <input
                            type="text"
                            name="telefono"
                            class="form-control delivery-input"
                            value="{{ old('telefono', $delivery->telefono) }}">

                    </div>


                    {{-- ESTADO --}}
                    <div class="col-md-6">

                        <label class="delivery-form-label">

                            <i class="bi bi-toggle-on"></i>

                            Estado

                        </label>


                        <select
                            name="estado"
                            class="form-select delivery-input"
                            required>

                            <option
                                value="activo"
                                {{ old('estado', $delivery->estado) === 'activo' ? 'selected' : '' }}>

                                Activo

                            </option>


                            <option
                                value="inactivo"
                                {{ old('estado', $delivery->estado) === 'inactivo' ? 'selected' : '' }}>

                                Inactivo

                            </option>

                        </select>

                    </div>


                    {{-- DIRECCION --}}
                    <div class="col-12">

                        <label class="delivery-form-label">

                            <i class="bi bi-geo-alt"></i>

                            Dirección

                        </label>


                        <textarea
                            name="direccion"
                            rows="3"
                            class="form-control delivery-input">{{ old('direccion', $delivery->direccion) }}</textarea>

                    </div>


                    {{-- PASSWORD --}}
                    <div class="col-md-6">

                        <label class="delivery-form-label">

                            <i class="bi bi-lock"></i>

                            Nueva contraseña

                        </label>


                        <input
                            type="password"
                            name="password"
                            class="form-control delivery-input"
                            placeholder="Dejar vacío para mantener actual">

                    </div>


                    {{-- CONFIRMAR PASSWORD --}}
                    <div class="col-md-6">

                        <label class="delivery-form-label">

                            <i class="bi bi-shield-lock"></i>

                            Confirmar nueva contraseña

                        </label>


                        <input
                            type="password"
                            name="password_confirmation"
                            class="form-control delivery-input"
                            placeholder="Repite la nueva contraseña">

                    </div>


                </div>


                {{-- INFO --}}
                <div class="delivery-info-box mt-4">

                    <i class="bi bi-info-circle"></i>

                    <div>

                        <strong>
                            Rol de usuario
                        </strong>

                        <p>
                            Este usuario pertenece al rol
                            <strong>Delivery (role_id = 3)</strong>.
                        </p>

                    </div>

                </div>


                {{-- BOTONES --}}
                <div class="delivery-form-actions">

                    <a
                        href="{{ route('admin.deliverys.index') }}"
                        class="btn btn-secondary">

                        <i class="bi bi-arrow-left"></i>

                        Cancelar

                    </a>


                    <button
                        type="submit"
                        class="btn btn-success">

                        <i class="bi bi-check-circle"></i>

                        Guardar cambios

                    </button>

                </div>


            </form>

        </div>

    </div>

</div>

@endsection