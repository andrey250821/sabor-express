@extends('layouts.admin')

@section('content')

<div class="container-fluid px-0 cliente-detalle-page">

    {{-- ENCABEZADO --}}
    <div class="cliente-detalle-header">

        <div>

            <h2 class="cliente-detalle-title">
                Cliente: {{ $cliente->name }}
            </h2>

            <p class="cliente-detalle-subtitle">
                Información e historial de pedidos
            </p>

        </div>


        <a href="{{ route('admin.clientes.index') }}"
            class="cliente-btn-volver">

            <i class="bi bi-arrow-left me-1"></i>
            Volver

        </a>

    </div>


    {{-- MENSAJES --}}
    @if(session('success'))

    <div class="alert alert-success alert-dismissible fade show clientes-alert">

        {{ session('success') }}

        <button type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

    @endif


    @if(session('error'))

    <div class="alert alert-danger alert-dismissible fade show clientes-alert">

        {{ session('error') }}

        <button type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

    @endif


    {{-- INFORMACIÓN DEL CLIENTE --}}
    <div class="cliente-info-card">


        {{-- CABECERA DEL CLIENTE --}}
        <div class="cliente-info-top">

            <div class="cliente-info-avatar">

                {{ strtoupper(substr($cliente->name, 0, 1)) }}

            </div>


            <div>

                <h4 class="cliente-info-name">

                    {{ $cliente->name }}

                </h4>

                <p class="cliente-info-email">

                    {{ $cliente->email }}

                </p>

            </div>

        </div>


        {{-- DATOS --}}
        <div class="cliente-datos">


            {{-- NOMBRE --}}
            <div class="cliente-dato">

                <span class="cliente-dato-label">
                    Nombre
                </span>

                <p class="cliente-dato-value">

                    {{ $cliente->name }}

                </p>

            </div>


            {{-- CORREO --}}
            <div class="cliente-dato">

                <span class="cliente-dato-label">
                    Correo electrónico
                </span>

                <p class="cliente-dato-value">

                    {{ $cliente->email }}

                </p>

            </div>


            {{-- TELEFONO --}}
            <div class="cliente-dato">

                <span class="cliente-dato-label">
                    Teléfono
                </span>

                <p class="cliente-dato-value">

                    {{ $cliente->telefono ?? 'No registrado' }}

                </p>

            </div>


            {{-- DIRECCION --}}
            <div class="cliente-dato">

                <span class="cliente-dato-label">
                    Dirección
                </span>

                <p class="cliente-dato-value">

                    {{ $cliente->direccion ?? 'No registrada' }}

                </p>

            </div>


            {{-- ESTADO --}}
            <div class="cliente-dato">

                <span class="cliente-dato-label">
                    Estado
                </span>

                <p class="cliente-dato-value">

                    @if($cliente->estado === 'activo')

                    <span class="cliente-estado activo">
                        Activo
                    </span>

                    @else

                    <span class="cliente-estado inactivo">
                        Inactivo
                    </span>

                    @endif

                </p>

            </div>


            {{-- PEDIDOS --}}
            <div class="cliente-dato">

                <span class="cliente-dato-label">
                    Total de pedidos
                </span>

                <p class="cliente-dato-value">

                    <span class="cliente-pedidos">

                        {{ $cliente->pedidos_count }}

                    </span>

                </p>

            </div>

        </div>

    </div>


    {{-- HISTORIAL --}}
    <div class="cliente-historial-header">

        <h3 class="cliente-historial-title">
            Historial de pedidos
        </h3>

        <p class="cliente-historial-subtitle">
            Pedidos realizados por este cliente
        </p>

    </div>


    {{-- TABLA HISTORIAL --}}
    <div class="cliente-historial-card">

        <div class="table-responsive">

            <table class="cliente-historial-table">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Fecha</th>

                        <th>Total</th>

                        <th>Estado</th>

                        <th>Productos</th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($cliente->pedidos as $pedido)

                    <tr>


                        {{-- ID --}}
                        <td>

                            <span class="cliente-id">
                                #{{ $pedido->id }}
                            </span>

                        </td>


                        {{-- FECHA --}}
                        <td>

                            {{ $pedido->created_at->format('d/m/Y H:i') }}

                        </td>


                        {{-- TOTAL --}}
                        <td>

                            <span class="cliente-total">

                                Bs. {{ number_format($pedido->total, 2) }}

                            </span>

                        </td>


                        {{-- ESTADO --}}
                        <td>

                            <span class="cliente-pedido-estado">

                                {{ ucfirst($pedido->estado) }}

                            </span>

                        </td>


                        {{-- PRODUCTOS --}}
                        <td>

                            @if($pedido->detallePedidos->count())

                            <ul class="cliente-productos">

                                @foreach($pedido->detallePedidos as $detalle)

                                <li>

                                    {{ $detalle->producto->nombre }}

                                    <span class="cliente-productos-cantidad">

                                        × {{ $detalle->cantidad }}

                                    </span>

                                </li>

                                @endforeach

                            </ul>

                            @else

                            <span class="cliente-productos-cantidad">
                                Sin productos
                            </span>

                            @endif

                        </td>


                    </tr>

                    @empty

                    <tr>

                        <td colspan="5"
                            class="clientes-empty">

                            <i class="bi bi-receipt clientes-empty-icon"></i>

                            Este cliente todavía no tiene pedidos.

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection