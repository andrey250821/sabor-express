@extends('layouts.admin')

@section('content')

<div class="delivery-form-page">

    <div class="delivery-form-card">


        {{-- HEADER --}}
        <div class="delivery-form-header">

            <div>

                <h3>

                    <i class="bi bi-person-plus-fill"></i>

                    Nuevo Repartidor

                </h3>

                <p>
                    Registra un nuevo repartidor para Sabor Express.
                </p>

            </div>


            <div class="delivery-form-icon">

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


        {{-- FORMULARIO --}}
        <div class="delivery-form-body">

            <form
                action="{{ route('admin.deliverys.store') }}"
                method="POST">

                @csrf


                <div class="row g-4">


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
                            value="{{ old('name') }}"
                            placeholder="Ej. Pedro Condori"
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
                            value="{{ old('email') }}"
                            placeholder="pedro@gmail.com"
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
                            value="{{ old('telefono') }}"
                            placeholder="Ej. 77777777">

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

                            <option value="activo"
                                {{ old('estado', 'activo') === 'activo' ? 'selected' : '' }}>

                                Activo

                            </option>

                            <option value="inactivo"
                                {{ old('estado') === 'inactivo' ? 'selected' : '' }}>

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
                            class="form-control delivery-input"
                            placeholder="Dirección del repartidor">{{ old('direccion') }}</textarea>

                    </div>


                    {{-- PASSWORD --}}
                    <div class="col-md-6">

                        <label class="delivery-form-label">

                            <i class="bi bi-lock"></i>

                            Contraseña

                        </label>


                        <input
                            type="password"
                            name="password"
                            class="form-control delivery-input"
                            placeholder="Mínimo 6 caracteres"
                            required>

                    </div>


                    {{-- CONFIRMAR --}}
                    <div class="col-md-6">

                        <label class="delivery-form-label">

                            <i class="bi bi-shield-lock"></i>

                            Confirmar contraseña

                        </label>


                        <input
                            type="password"
                            name="password_confirmation"
                            class="form-control delivery-input"
                            placeholder="Repite la contraseña"
                            required>

                    </div>


                </div>


                {{-- INFORMACION --}}
                <div class="delivery-info-box mt-4">

                    <i class="bi bi-info-circle"></i>

                    <div>

                        <strong>
                            Rol de repartidor
                        </strong>

                        <p>
                            Este usuario será creado automáticamente con
                            <strong>role_id = 3</strong>.
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

                        <i class="bi bi-person-plus"></i>

                        Crear repartidor

                    </button>

                </div>


            </form>

        </div>

    </div>

</div>

@endsection