@extends('layouts.admin')

@section('title', 'Reportes administrativos')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-reportes.css') }}">
@endpush

@section('content')
<div class="reportes-page container-fluid px-0">
    <header class="reportes-header">
        <div>
            <span class="reportes-eyebrow">CONTROL Y SUPERVISIÓN</span>
            <h2 class="reportes-title"><i class="bi bi-file-earmark-bar-graph-fill"></i> Reportes administrativos</h2>
            <p class="reportes-subtitle">
                Selecciona la información y el periodo. El PDF se descargará listo para guardar o imprimir.
            </p>
        </div>
        <div class="reportes-header-icon"><i class="bi bi-file-earmark-pdf-fill"></i></div>
    </header>

    @if ($errors->any())
        <div class="alert alert-danger reportes-alert" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>
                <strong>No se pudo generar el reporte.</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.reportes.pdf') }}" method="GET" id="form-reportes">
        <section class="reportes-panel">
            <div class="reportes-section-heading">
                <span class="reportes-step">01</span>
                <div>
                    <h3>Tipo de reporte</h3>
                    <p>Elige qué información necesitas consultar.</p>
                </div>
            </div>

            <div class="reportes-types">
                @foreach ($tipos as $clave => $opcion)
                    <label class="reportes-type-card {{ $tipo === $clave ? 'is-selected' : '' }}" for="tipo-{{ $clave }}">
                        <input
                            type="radio"
                            name="tipo"
                            id="tipo-{{ $clave }}"
                            value="{{ $clave }}"
                            {{ $tipo === $clave ? 'checked' : '' }}
                            required>
                        <span class="reportes-type-icon"><i class="bi {{ $opcion['icono'] }}"></i></span>
                        <span class="reportes-type-text">
                            <strong>{{ $opcion['titulo'] }}</strong>
                            <small>{{ $opcion['descripcion'] }}</small>
                        </span>
                        <span class="reportes-type-check"><i class="bi bi-check-circle-fill"></i></span>
                    </label>
                @endforeach
            </div>
        </section>

        <section class="reportes-panel reportes-period-panel">
            <div class="reportes-section-heading">
                <span class="reportes-step">02</span>
                <div>
                    <h3>Periodo del informe</h3>
                    <p>Se incluyen registros con fecha de actividad dentro de ambas fechas.</p>
                </div>
            </div>

            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-5">
                    <label for="desde" class="reportes-label">Desde</label>
                    <div class="reportes-date-field">
                        <i class="bi bi-calendar-event"></i>
                        <input type="date" id="desde" name="desde" value="{{ $desde }}" max="{{ $hoy->toDateString() }}" required>
                    </div>
                </div>
                <div class="col-12 col-md-5">
                    <label for="hasta" class="reportes-label">Hasta</label>
                    <div class="reportes-date-field">
                        <i class="bi bi-calendar-check"></i>
                        <input type="date" id="hasta" name="hasta" value="{{ $hasta }}" max="{{ $hoy->toDateString() }}" required>
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <button type="button" class="reportes-btn-periodo" id="periodo-mes">
                        <i class="bi bi-calendar-month"></i>
                        Mes actual
                    </button>
                </div>
            </div>

            <div class="reportes-range-note">
                <i class="bi bi-info-circle-fill"></i>
                <span>Las fechas se interpretan con la hora de Bolivia. Los pedidos cancelados no se contabilizan como ventas confirmadas.</span>
            </div>
        </section>

        <section class="reportes-download-panel">
            <div class="reportes-download-copy">
                <div class="reportes-download-icon"><i class="bi bi-filetype-pdf"></i></div>
                <div>
                    <h3>Generar documento PDF</h3>
                    <p>Incluye el nombre y logo del restaurante, el periodo seleccionado, un resumen y el detalle correspondiente.</p>
                </div>
            </div>

            <button type="submit" class="reportes-btn-download">
                <i class="bi bi-download"></i>
                Generar y descargar PDF
            </button>
        </section>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cards = document.querySelectorAll('.reportes-type-card');
    const form = document.getElementById('form-reportes');
    const desde = document.getElementById('desde');
    const hasta = document.getElementById('hasta');
    const mesActual = document.getElementById('periodo-mes');

    const actualizarTipo = () => {
        cards.forEach((card) => {
            const input = card.querySelector('input[name="tipo"]');
            card.classList.toggle('is-selected', Boolean(input && input.checked));
        });
    };

    cards.forEach((card) => {
        const input = card.querySelector('input[name="tipo"]');
        if (input) input.addEventListener('change', actualizarTipo);
    });

    mesActual?.addEventListener('click', function () {
        const hoy = new Date();
        const primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        const formato = (fecha) => [
            fecha.getFullYear(),
            String(fecha.getMonth() + 1).padStart(2, '0'),
            String(fecha.getDate()).padStart(2, '0')
        ].join('-');

        desde.value = formato(primerDia);
        hasta.value = formato(hoy);
        desde.dispatchEvent(new Event('change', { bubbles: true }));
        hasta.dispatchEvent(new Event('change', { bubbles: true }));
    });

    form?.addEventListener('submit', function (event) {
        if (desde.value && hasta.value && desde.value > hasta.value) {
            event.preventDefault();
            hasta.setCustomValidity('La fecha final debe ser igual o posterior a la fecha inicial.');
            hasta.reportValidity();
            return;
        }

        hasta.setCustomValidity('');
    });

    hasta?.addEventListener('change', () => hasta.setCustomValidity(''));
    actualizarTipo();
});
</script>
@endsection
