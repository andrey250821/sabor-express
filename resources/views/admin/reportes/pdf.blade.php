<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $tituloReporte }}</title>
    <style>
        @page { margin: 22px 26px 34px 26px; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; color: #20212a; font-size: 9px; line-height: 1.45; }
        .header { width: 100%; border-bottom: 3px solid #8b1e45; padding-bottom: 12px; margin-bottom: 18px; }
        .header td { vertical-align: middle; }
        .brand-logo { width: 72px; max-height: 58px; }
        .brand-fallback { width: 54px; height: 54px; background: #8b1e45; color: #fff; font-size: 20px; font-weight: bold; text-align: center; padding-top: 14px; }
        .restaurant-name { font-size: 19px; color: #681631; font-weight: bold; margin: 0 0 3px; }
        .document-label { color: #737783; text-transform: uppercase; font-size: 8px; letter-spacing: 1.3px; }
        .meta { text-align: right; color: #545765; font-size: 8px; }
        .meta strong { color: #22232b; }
        .report-heading { margin: 0 0 14px; }
        .report-heading h2 { color: #681631; margin: 0 0 4px; font-size: 16px; }
        .report-heading p { margin: 0; color: #636673; font-size: 9px; }
        .kpis { width: 100%; margin: 0 0 17px; border-collapse: separate; border-spacing: 6px 0; }
        .kpi { border: 1px solid #e0e1e6; background: #faf8f9; padding: 9px 10px; vertical-align: top; }
        .kpi-label { color: #727480; font-size: 7.5px; text-transform: uppercase; letter-spacing: .4px; }
        .kpi-value { color: #681631; font-size: 13px; font-weight: bold; margin-top: 4px; }
        h3 { color: #681631; font-size: 11px; margin: 15px 0 7px; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 11px; }
        table.data thead { display: table-header-group; }
        table.data tr { page-break-inside: avoid; }
        table.data th { background: #681631; color: #fff; padding: 7px 6px; text-align: left; font-size: 7.5px; }
        table.data td { border-bottom: 1px solid #e8e8ec; padding: 6px; vertical-align: top; font-size: 7.5px; }
        table.data tr:nth-child(even) td { background: #fbfbfc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .muted { color: #747783; }
        .empty { border: 1px dashed #c9cbd2; padding: 16px; text-align: center; color: #6d707b; }
        .note { background: #faf6f8; border-left: 3px solid #8b1e45; padding: 8px 10px; color: #626570; font-size: 8px; margin-top: 12px; }
        .footer { position: fixed; bottom: -22px; left: 0; right: 0; border-top: 1px solid #e1e1e5; padding-top: 5px; text-align: right; color: #868894; font-size: 7px; }
        .state { text-transform: capitalize; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td style="width:80px;">
                @if($logoDataUri)
                    <img class="brand-logo" src="{{ $logoDataUri }}" alt="Logo">
                @else
                    <div class="brand-fallback">SE</div>
                @endif
            </td>
            <td>
                <h1 class="restaurant-name">{{ $restaurante->nombre_restaurante ?? 'Sabor Express' }}</h1>
                <div class="document-label">Informe administrativo · Sabor Express</div>
                @if($restaurante?->direccion)
                    <div class="muted">{{ $restaurante->direccion }}</div>
                @endif
                @if($restaurante?->telefono)
                    <div class="muted">Tel.: {{ $restaurante->telefono }}</div>
                @endif
            </td>
            <td class="meta" style="width:230px;">
                <div><strong>Periodo:</strong> {{ $periodoDesde->format('d/m/Y') }} – {{ $periodoHasta->format('d/m/Y') }}</div>
                <div><strong>Generado por:</strong> {{ $generadoPor }}</div>
                <div><strong>Emitido:</strong> {{ $generadoEn->format('d/m/Y H:i') }}</div>
                <div><strong>Zona horaria:</strong> Bolivia</div>
            </td>
        </tr>
    </table>

    <div class="report-heading">
        <h2>{{ $tituloReporte }}</h2>
        <p>{{ $descripcionReporte }}</p>
    </div>

    <table class="kpis">
        <tr>
            @foreach($resumen as $indicador)
                <td class="kpi">
                    <div class="kpi-label">{{ $indicador['etiqueta'] }}</div>
                    <div class="kpi-value">{{ $indicador['valor'] }}</div>
                </td>
            @endforeach
        </tr>
    </table>

    @if($tipo === 'pedidos')
        <h3>Detalle de pedidos</h3>
        @if(count($filas))
            <table class="data">
                <thead>
                    <tr>
                        <th>Pedido</th><th>Fecha</th><th>Cliente</th><th>Correo</th>
                        <th>Cocinero</th><th>Delivery</th><th>Productos</th><th>Estado</th><th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($filas as $fila)
                        <tr>
                            <td>{{ $fila['pedido'] }}</td>
                            <td>{{ $fila['fecha'] }}</td>
                            <td>{{ $fila['cliente'] }}</td>
                            <td>{{ $fila['correo'] }}</td>
                            <td>{{ $fila['cocinero'] }}</td>
                            <td>{{ $fila['delivery'] }}</td>
                            <td>{{ $fila['productos'] }}</td>
                            <td class="state">{{ $fila['estado'] }}</td>
                            <td class="text-right">Bs {{ number_format($fila['total'], 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty">No existen pedidos registrados en el periodo seleccionado.</div>
        @endif
    @elseif($tipo === 'ventas')
        <h3>Resumen diario de ventas confirmadas</h3>
        @if(count($ventasPorDia))
            <table class="data">
                <thead><tr><th>Fecha</th><th class="text-center">Pedidos confirmados</th><th class="text-right">Importe vendido</th></tr></thead>
                <tbody>
                    @foreach($ventasPorDia as $dia)
                        <tr>
                            <td>{{ $dia['fecha'] }}</td>
                            <td class="text-center">{{ $dia['pedidos'] }}</td>
                            <td class="text-right">Bs {{ number_format($dia['total'], 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty">No hay ventas confirmadas en el periodo seleccionado.</div>
        @endif

        <h3>Detalle de ventas</h3>
        @if(count($filas))
            <table class="data">
                <thead><tr><th>Pedido</th><th>Fecha</th><th>Cliente</th><th>Correo</th><th>Estado</th><th class="text-right">Importe</th></tr></thead>
                <tbody>
                    @foreach($filas as $fila)
                        <tr>
                            <td>{{ $fila['pedido'] }}</td><td>{{ $fila['fecha'] }}</td><td>{{ $fila['cliente'] }}</td>
                            <td>{{ $fila['correo'] }}</td><td class="state">{{ $fila['estado'] }}</td>
                            <td class="text-right">Bs {{ number_format($fila['total'], 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty">No hay ventas confirmadas en el periodo seleccionado.</div>
        @endif
    @elseif($tipo === 'entregas')
        <h3>Entregas completadas</h3>
        @if(count($filas))
            <table class="data">
                <thead>
                    <tr><th>Pedido</th><th>Fecha de entrega</th><th>Cliente</th><th>Delivery</th><th class="text-right">Distancia</th><th class="text-right">Tarifa</th><th class="text-right">Comisión Delivery</th><th class="text-right">Parte restaurante</th></tr>
                </thead>
                <tbody>
                    @foreach($filas as $fila)
                        <tr>
                            <td>{{ $fila['pedido'] }}</td><td>{{ $fila['fecha'] }}</td><td>{{ $fila['cliente'] }}</td><td>{{ $fila['delivery'] }}</td>
                            <td class="text-right">{{ number_format($fila['distancia'], 2, ',', '.') }} km</td>
                            <td class="text-right">Bs {{ number_format($fila['tarifa'], 2, ',', '.') }}</td>
                            <td class="text-right">Bs {{ number_format($fila['comision'], 2, ',', '.') }}</td>
                            <td class="text-right">Bs {{ number_format($fila['restaurante'], 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty">No se completaron entregas en el periodo seleccionado.</div>
        @endif
    @elseif($tipo === 'personal')
        <h3>Actividad de cocina</h3>
        @if(count($cocineros))
            <table class="data">
                <thead><tr><th>Cocinero</th><th>Correo</th><th>Estado de cuenta</th><th class="text-center">Preparaciones iniciadas</th><th class="text-center">Pedidos listos</th><th class="text-center">En preparación al generar</th></tr></thead>
                <tbody>
                    @foreach($cocineros as $fila)
                        <tr>
                            <td>{{ $fila['nombre'] }}</td><td>{{ $fila['correo'] }}</td><td>{{ ucfirst($fila['estado'] ?? 'sin estado') }}</td>
                            <td class="text-center">{{ $fila['iniciados'] }}</td><td class="text-center">{{ $fila['listos'] }}</td><td class="text-center">{{ $fila['preparando'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty">No hay cocineros registrados.</div>
        @endif

        <h3>Actividad de Delivery</h3>
        @if(count($deliverys))
            <table class="data">
                <thead><tr><th>Delivery</th><th>Correo</th><th>Estado de cuenta</th><th class="text-center">Asignados en el periodo</th><th class="text-center">Entregas iniciadas</th><th class="text-center">Entregas completadas</th><th class="text-center">Pedidos activos ahora</th><th class="text-right">Distancia completada</th><th class="text-right">Comisión generada</th></tr></thead>
                <tbody>
                    @foreach($deliverys as $fila)
                        <tr>
                            <td>{{ $fila['nombre'] }}</td><td>{{ $fila['correo'] }}</td><td>{{ ucfirst($fila['estado'] ?? 'sin estado') }}</td>
                            <td class="text-center">{{ $fila['asignados'] }}</td><td class="text-center">{{ $fila['iniciados'] }}</td><td class="text-center">{{ $fila['entregados'] }}</td><td class="text-center">{{ $fila['activos'] }}</td>
                            <td class="text-right">{{ number_format($fila['distancia'], 2, ',', '.') }} km</td><td class="text-right">Bs {{ number_format($fila['comision'], 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty">No hay usuarios de Delivery registrados.</div>
        @endif
    @endif

    @if($notaReporte)
        <div class="note"><strong>Nota:</strong> {{ $notaReporte }}</div>
    @endif

    <div class="footer">
        {{ $restaurante->nombre_restaurante ?? 'Sabor Express' }} · Documento generado automáticamente · {{ $generadoEn->format('d/m/Y H:i') }}
    </div>
</body>
</html>
