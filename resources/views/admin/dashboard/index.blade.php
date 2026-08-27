
@extends('layouts.admin')

@section('content')

<div class="container-fluid px-0">


    <!-- TITULO -->

    <div class="mb-4">

        <h2 class="fw-bold text-white">
            Dashboard
        </h2>


        <p class="text-muted">
            Resumen general de Sabor Express
        </p>

    </div>




    <!-- ==========================
        TARJETAS PRINCIPALES
    =========================== -->


    <div class="row g-4">



        <!-- PEDIDOS -->

        <div class="col-12 col-sm-6 col-xl-3">


            <a href="{{ route('admin.pedidos.index') }}"
                class="text-decoration-none">


                <div class="card dashboard-card h-100 shadow">


                    <div class="card-body">



                        <div class="icon-box bg-danger">

                            <i class="bi bi-bag"></i>

                        </div>




                        <h6 class="text-muted mt-3">

                            Pedidos

                        </h6>



                        <h2 class="fw-bold text-white">

                            {{ $pedidos }}

                        </h2>



                        <small class="text-secondary">

                            Pedidos registrados

                        </small>



                        <div class="text-end mt-3">

                            <small class="ver-detalle">

                                Ver pedidos →

                            </small>


                        </div>


                    </div>


                </div>


            </a>


        </div>







        <!-- CLIENTES -->


        <div class="col-12 col-sm-6 col-xl-3">


            <a href="{{ route('admin.clientes.index') }}"
                class="text-decoration-none">


                <div class="card dashboard-card h-100 shadow">


                    <div class="card-body">


                        <div class="icon-box bg-success">

                            <i class="bi bi-people"></i>

                        </div>



                        <h6 class="text-muted mt-3">

                            Clientes

                        </h6>




                        <h2 class="fw-bold text-white">

                            {{ $clientes }}

                        </h2>




                        <small class="text-secondary">

                            Usuarios registrados

                        </small>



                        <div class="text-end mt-3">


                            <small class="ver-detalle">

                                Ver clientes →

                            </small>


                        </div>



                    </div>


                </div>


            </a>


        </div>









        <!-- DELIVERY -->


        <div class="col-12 col-sm-6 col-xl-3">



            <a href="{{ route('admin.deliverys.index') }}"
                class="text-decoration-none">



                <div class="card dashboard-card h-100 shadow">


                    <div class="card-body">



                        <div class="icon-box bg-warning">


                            <i class="bi bi-bicycle"></i>


                        </div>





                        <h6 class="text-muted mt-3">


                            Delivery


                        </h6>





                        <h2 class="fw-bold text-white">


                            {{ $deliverys }}


                        </h2>





                        <small class="text-secondary">


                            Repartidores activos


                        </small>





                        <div class="text-end mt-3">


                            <small class="ver-detalle">


                                Ver deliverys →


                            </small>


                        </div>




                    </div>



                </div>



            </a>



        </div>








        <!-- PRODUCTOS -->


        <div class="col-12 col-sm-6 col-xl-3">



            <a href="{{ route('admin.productos.index') }}"
                class="text-decoration-none">



                <div class="card dashboard-card h-100 shadow">



                    <div class="card-body">



                        <div class="icon-box bg-primary">


                            <i class="bi bi-box-seam"></i>


                        </div>





                        <h6 class="text-muted mt-3">


                            Productos


                        </h6>





                        <h2 class="fw-bold text-white">


                            {{ $productos }}


                        </h2>





                        <small class="text-secondary">


                            Productos disponibles


                        </small>





                        <div class="text-end mt-3">


                            <small class="ver-detalle">


                                Ver productos →


                            </small>


                        </div>



                    </div>


                </div>



            </a>



        </div>



    </div>








    <!-- ==========================
        SEGUNDA FILA
    =========================== -->



    <div class="row g-4 mt-1">



        <!-- VENTAS -->


        <div class="col-12 col-lg-4">


            <div class="card dashboard-card h-100 shadow">


                <div class="card-body">


                    <div class="icon-box bg-danger">


                        <i class="bi bi-cash"></i>


                    </div>



                    <h6 class="text-muted mt-3">

                        Ventas del mes

                    </h6>




                    <h2 class="fw-bold text-white">


                        Bs {{ number_format($ventasMes,2) }}


                    </h2>



                    <small class="text-secondary">

                        Ingresos actuales

                    </small>



                </div>


            </div>


        </div>






        <!-- COMPROBANTES -->


        <div class="col-12 col-lg-4">


            <a href="{{ route('admin.comprobantes.index') }}"
                class="text-decoration-none">


                <div class="card dashboard-card h-100 shadow">



                    <div class="card-body">


                        <h5 class="fw-bold text-white">

                            Comprobantes pendientes

                        </h5>



                        <hr>



                        <h1 class="text-danger">


                            {{ $comprobantesPendientes }}


                        </h1>



                        <small class="text-muted">

                            Esperando revisión

                        </small>


                    </div>


                </div>


            </a>


        </div>
        <!-- ESTADO DEL SISTEMA -->


        <div class="col-12 col-lg-4">


            <div class="card dashboard-card h-100 shadow">


                <div class="card-body">


                    <h5 class="fw-bold text-white">

                        Estado del sistema

                    </h5>


                    <hr>



                    <span class="badge bg-success p-2">

                        Sistema operativo

                    </span>



                    <div class="mt-3">


                        <small class="text-muted">

                            Todos los servicios funcionando correctamente

                        </small>


                    </div>



                </div>


            </div>


        </div>



    </div>








    <!-- ==========================
        ULTIMOS PEDIDOS
    =========================== -->


    <div class="row mt-4">


        <div class="col-12">


            <div class="card dashboard-card shadow">


                <div class="card-body">



                    <div class="d-flex flex-column flex-md-row 
                                justify-content-between 
                                align-items-md-center 
                                gap-2 mb-3">



                        <h5 class="fw-bold text-white mb-0">


                            <i class="bi bi-clock-history text-danger"></i>

                            Últimos pedidos


                        </h5>




                        <a href="{{ route('admin.pedidos.index') }}"
                            class="btn btn-sm btn-outline-light">


                            Ver todos


                        </a>



                    </div>






                    <div class="table-responsive">



                        <table class="table table-dark table-hover align-middle">



                            <thead>


                                <tr>


                                    <th>#</th>

                                    <th>Cliente</th>

                                    <th>Total</th>

                                    <th>Estado</th>

                                    <th>Fecha</th>


                                </tr>


                            </thead>





                            <tbody>



                                @forelse($ultimosPedidos as $pedido)



                                <tr>



                                    <td>

                                        #{{ $pedido->id }}

                                    </td>





                                    <td>


                                        {{ $pedido->user->name ?? 'Cliente eliminado' }}


                                    </td>






                                    <td>


                                        Bs {{ number_format($pedido->total,2) }}


                                    </td>







                                    <td>



                                        @if($pedido->estado == 'pendiente')


                                        <span class="badge bg-warning text-dark">

                                            Pendiente

                                        </span>




                                        @elseif($pedido->estado == 'asignado')


                                        <span class="badge bg-info">

                                            Asignado

                                        </span>




                                        @elseif($pedido->estado == 'entregado')


                                        <span class="badge bg-success">

                                            Entregado

                                        </span>




                                        @else


                                        <span class="badge bg-secondary">


                                            {{ ucfirst($pedido->estado) }}


                                        </span>



                                        @endif



                                    </td>







                                    <td>


                                        {{ $pedido->created_at->format('d/m/Y H:i') }}


                                    </td>




                                </tr>





                                @empty



                                <tr>


                                    <td colspan="5"
                                        class="text-center text-muted">


                                        No existen pedidos registrados.


                                    </td>


                                </tr>



                                @endforelse




                            </tbody>




                        </table>




                    </div>



                </div>


            </div>


        </div>


    </div>









    <!-- ==========================
        GRAFICO VENTAS
    =========================== -->



    <div class="row mt-4">


        <div class="col-12">



            <div class="card dashboard-card shadow">



                <div class="card-body">



                    <h5 class="fw-bold text-white mb-3">


                        <i class="bi bi-graph-up-arrow text-danger"></i>


                        Ventas últimos 7 días



                    </h5>






                    <div style="height:300px;">


                        <canvas id="ventasChart"></canvas>


                    </div>




                </div>



            </div>



        </div>


    </div>




</div>







@push('scripts')



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>





<script>
    const ventas = @json($ventasSemana);



    const canvas = document.getElementById('ventasChart');



    if (canvas && ventas.length > 0) {



        new Chart(canvas, {


            type: 'line',



            data: {



                labels: ventas.map(v => v.fecha),



                datasets: [{


                    label: 'Ventas Bs',



                    data: ventas.map(v => Number(v.total)),



                    borderColor: '#8b1e45',



                    backgroundColor: 'rgba(139,30,69,.20)',



                    borderWidth: 3,



                    fill: true,



                    tension: .4,



                    pointRadius: 5,



                    pointBackgroundColor: '#ff4d88'


                }]


            },






            options: {



                responsive: true,



                maintainAspectRatio: false,



                plugins: {



                    legend: {


                        display: false


                    }


                },




                scales: {



                    x: {


                        ticks: {
                            color: '#ddd'
                        },


                        grid: {
                            color: '#333'
                        }


                    },





                    y: {


                        ticks: {
                            color: '#ddd'
                        },


                        grid: {
                            color: '#333'
                        }


                    }



                }



            }





        });



    }
</script>



@endpush



@endsection