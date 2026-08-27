@extends('layouts.admin')

@section('content')

<div class="container-fluid px-0 comprobantes-page">


    {{-- =====================================================
        ENCABEZADO
    ====================================================== --}}

    <div class="d-flex flex-column flex-md-row
                justify-content-between
                align-items-md-center
                gap-3 mb-4">

        <div>

            <h2 class="fw-bold text-white mb-1">

                <i class="bi bi-receipt text-danger"></i>

                Gestión de Comprobantes

            </h2>

            <p class="text-secondary mb-0">

                Revisa y administra los comprobantes de pago
                de los pedidos.

            </p>

        </div>


        {{-- TOTAL DEL ESTADO ACTUAL --}}

        <div class="comprobante-total">

            <div class="comprobante-total-icon">

                <i class="bi bi-file-earmark-check"></i>

            </div>

            <div>

                <small class="text-secondary d-block">

                    Comprobantes {{ ucfirst($estado) }}

                </small>

                <strong>

                    {{ $comprobantes->count() }}

                </strong>

            </div>

        </div>

    </div>




    {{-- =====================================================
        TARJETAS DE ESTADO
    ====================================================== --}}

    <div class="row g-3 mb-4">


        {{-- PENDIENTES --}}

        <div class="col-12 col-md-4">

            <a href="{{ route('admin.comprobantes.index','pendiente') }}"
                class="text-decoration-none">

                <div class="comprobante-status-card
                    status-pendiente
                    {{ $estado == 'pendiente' ? 'active' : '' }}">

                    <div class="status-icon">

                        <i class="bi bi-hourglass-split"></i>

                    </div>

                    <div class="status-info">

                        <span>
                            Pendientes
                        </span>

                        <strong>
                            {{ $pendientes }}
                        </strong>

                    </div>

                    <i class="bi bi-chevron-right status-arrow"></i>

                </div>

            </a>

        </div>




        {{-- APROBADOS --}}

        <div class="col-12 col-md-4">

            <a href="{{ route('admin.comprobantes.index','aprobado') }}"
                class="text-decoration-none">

                <div class="comprobante-status-card
                    status-aprobado
                    {{ $estado == 'aprobado' ? 'active' : '' }}">

                    <div class="status-icon">

                        <i class="bi bi-check-circle"></i>

                    </div>

                    <div class="status-info">

                        <span>
                            Aprobados
                        </span>

                        <strong>
                            {{ $aprobados }}
                        </strong>

                    </div>

                    <i class="bi bi-chevron-right status-arrow"></i>

                </div>

            </a>

        </div>




        {{-- RECHAZADOS --}}

        <div class="col-12 col-md-4">

            <a href="{{ route('admin.comprobantes.index','rechazado') }}"
                class="text-decoration-none">

                <div class="comprobante-status-card
                    status-rechazado
                    {{ $estado == 'rechazado' ? 'active' : '' }}">

                    <div class="status-icon">

                        <i class="bi bi-x-circle"></i>

                    </div>

                    <div class="status-info">

                        <span>
                            Rechazados
                        </span>

                        <strong>
                            {{ $rechazados }}
                        </strong>

                    </div>

                    <i class="bi bi-chevron-right status-arrow"></i>

                </div>

            </a>

        </div>

    </div>




    {{-- =====================================================
        TÍTULO DEL LISTADO
    ====================================================== --}}

    <div class="comprobantes-section-header mb-3">

        <div>

            <h4 class="fw-bold text-white mb-1">

                Comprobantes {{ ucfirst($estado) }}

            </h4>

            <small class="text-secondary">

                Lista de comprobantes disponibles para revisión.

            </small>

        </div>

    </div>




    {{-- =====================================================
        COMPROBANTES
    ====================================================== --}}

    <div class="row g-4">


        @forelse($comprobantes as $comprobante)


        <div class="col-12 col-md-6 col-xl-4">


            <div class="comprobante-card">


                {{-- CABECERA --}}

                <div class="comprobante-card-header">


                    <div class="d-flex
                                justify-content-between
                                align-items-center">


                        <span class="pedido-numero">

                            <i class="bi bi-bag"></i>

                            Pedido #{{ $comprobante->pedido->id }}

                        </span>


                        @if($comprobante->estado == 'pendiente')

                        <span class="estado-badge pendiente">

                            Pendiente

                        </span>

                        @elseif($comprobante->estado == 'aprobado')

                        <span class="estado-badge aprobado">

                            Aprobado

                        </span>

                        @else

                        <span class="estado-badge rechazado">

                            Rechazado

                        </span>

                        @endif


                    </div>

                </div>




                {{-- CUERPO --}}

                <div class="comprobante-card-body">


                    {{-- CLIENTE --}}

                    <div class="comprobante-info">


                        <div class="info-icon">

                            <i class="bi bi-person"></i>

                        </div>


                        <div>

                            <small>
                                Cliente
                            </small>

                            <strong>

                                {{ $comprobante->pedido->user->name }}

                            </strong>

                        </div>


                    </div>




                    {{-- TELÉFONO --}}

                    <div class="comprobante-info">


                        <div class="info-icon">

                            <i class="bi bi-telephone"></i>

                        </div>


                        <div>

                            <small>
                                Teléfono
                            </small>

                            <strong>

                                {{ $comprobante->pedido->user->telefono ?? 'Sin teléfono' }}

                            </strong>

                        </div>


                    </div>




                    {{-- DIRECCIÓN --}}

                    <div class="comprobante-info">


                        <div class="info-icon">

                            <i class="bi bi-geo-alt"></i>

                        </div>


                        <div>

                            <small>
                                Dirección
                            </small>

                            <strong>

                                {{ $comprobante->pedido->direccion_entrega }}

                            </strong>

                        </div>


                    </div>




                    {{-- REFERENCIA --}}

                    @if($comprobante->pedido->referencia_delivery)

                    <div class="comprobante-info">


                        <div class="info-icon">

                            <i class="bi bi-pin-map"></i>

                        </div>


                        <div>

                            <small>
                                Referencia
                            </small>

                            <strong>

                                {{ $comprobante->pedido->referencia_delivery }}

                            </strong>

                        </div>


                    </div>

                    @endif




                    {{-- TOTAL --}}

                    <div class="comprobante-monto">

                        <div>

                            <small>
                                Total del pedido
                            </small>

                            <strong>

                                Bs {{ number_format($comprobante->pedido->total, 2) }}

                            </strong>

                        </div>

                        <i class="bi bi-cash-stack"></i>

                    </div>




                    {{-- IMAGEN --}}

                    <div class="comprobante-imagen-container">


                        <img
                            src="{{ asset('storage/'.$comprobante->imagen) }}"
                            class="img-fluid rounded-3 comprobante-img mb-3"
                            alt="Comprobante de pago">


                    </div>




                    {{-- VER COMPROBANTE --}}

                    <a
                        href="{{ asset('storage/'.$comprobante->imagen) }}"
                        target="_blank"
                        class="btn btn-outline-light w-100 mb-3">

                        👁 Ver comprobante

                    </a>




                    {{-- =================================================
                        ACCIONES
                    ================================================== --}}


                    @if($comprobante->estado == 'pendiente')


                    <div class="comprobante-acciones mt-3">


                        <form
                            action="{{ route('admin.comprobantes.aprobar',$comprobante->id) }}"
                            method="POST"
                            class="flex-fill">

                            @csrf
                            @method('PUT')

                            <button
                                type="submit"
                                class="btn btn-success w-100">

                                <i class="bi bi-check-lg"></i>

                                Aprobar

                            </button>

                        </form>



                        <form
                            action="{{ route('admin.comprobantes.rechazar',$comprobante->id) }}"
                            method="POST"
                            class="flex-fill">

                            @csrf
                            @method('PUT')

                            <button
                                type="submit"
                                class="btn btn-danger w-100">

                                <i class="bi bi-x-lg"></i>

                                Rechazar

                            </button>

                        </form>


                    </div>


                    @else


                    <div class="estado-final">


                        @if($comprobante->estado == 'aprobado')

                        <i class="bi bi-check-circle-fill"></i>

                        <span>
                            Comprobante aprobado
                        </span>

                        @else

                        <i class="bi bi-x-circle-fill"></i>

                        <span>
                            Comprobante rechazado
                        </span>

                        @endif


                    </div>


                    @endif


                </div>


            </div>


        </div>


        @empty


        {{-- SIN COMPROBANTES --}}

        <div class="col-12">


            <div class="sin-comprobantes">


                <div class="sin-comprobantes-icon">

                    <i class="bi bi-inbox"></i>

                </div>


                <h5 class="text-white fw-bold">

                    No existen comprobantes

                </h5>


                <p class="text-secondary mb-0">

                    No hay comprobantes
                    {{ $estado }}
                    para mostrar.

                </p>


            </div>


        </div>


        @endforelse


    </div>


</div>

@endsection