<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AsignacionDelivery;
use App\Models\Configuracion;
use App\Models\Pedido;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReportesController extends Controller
{
    private const TIMEZONE = 'America/La_Paz';

    private const ESTADOS_VENTA = [
        'pagado',
        'preparando',
        'listo',
        'asignado',
        'en_camino',
        'entregado',
    ];

    /**
     * Pantalla donde el administrador selecciona el tipo de reporte y el periodo.
     */
    public function index(Request $request): View
    {
        $hoy = Carbon::now(self::TIMEZONE);
        $desdePorDefecto = $hoy->copy()->startOfMonth()->toDateString();
        $hastaPorDefecto = $hoy->toDateString();

        $tipos = $this->tiposDisponibles();
        $tipo = (string) $request->query('tipo', 'pedidos');

        if (!array_key_exists($tipo, $tipos)) {
            $tipo = 'pedidos';
        }

        $desde = $this->fechaParaFormulario(
            $request->query('desde'),
            $desdePorDefecto
        );

        $hasta = $this->fechaParaFormulario(
            $request->query('hasta'),
            $hastaPorDefecto
        );

        if ($desde > $hasta) {
            $desde = $desdePorDefecto;
            $hasta = $hastaPorDefecto;
        }

        return view('admin.reportes.index', compact(
            'tipos',
            'tipo',
            'desde',
            'hasta',
            'hoy'
        ));
    }

    /**
     * Generar y descargar el PDF seleccionado.
     */
    public function descargar(Request $request)
    {
        $datos = $request->validate([
            'tipo' => 'required|in:pedidos,ventas,entregas,personal',
            'desde' => 'required|date_format:Y-m-d',
            'hasta' => 'required|date_format:Y-m-d|after_or_equal:desde',
        ], [
            'tipo.required' => 'Selecciona el tipo de reporte.',
            'tipo.in' => 'El tipo de reporte seleccionado no es válido.',
            'desde.required' => 'Selecciona la fecha inicial.',
            'desde.date_format' => 'La fecha inicial no es válida.',
            'hasta.required' => 'Selecciona la fecha final.',
            'hasta.date_format' => 'La fecha final no es válida.',
            'hasta.after_or_equal' => 'La fecha final debe ser igual o posterior a la fecha inicial.',
        ]);

        $desde = Carbon::createFromFormat(
            '!Y-m-d',
            $datos['desde'],
            self::TIMEZONE
        )->startOfDay();

        $hasta = Carbon::createFromFormat(
            '!Y-m-d',
            $datos['hasta'],
            self::TIMEZONE
        )->endOfDay();

        $inicioUtc = $desde->copy()->utc();
        $finUtc = $hasta->copy()->utc();

        $tipo = $datos['tipo'];
        $meta = $this->tiposDisponibles()[$tipo];
        $configuracion = Configuracion::query()->first();

        $reporte = match ($tipo) {
            'pedidos' => $this->reportePedidos($inicioUtc, $finUtc),
            'ventas' => $this->reporteVentas($inicioUtc, $finUtc),
            'entregas' => $this->reporteEntregas($inicioUtc, $finUtc),
            'personal' => $this->reportePersonal($desde, $hasta),
        };

        $pdf = Pdf::loadView('admin.reportes.pdf', array_merge($reporte, [
            'tipo' => $tipo,
            'tituloReporte' => $meta['titulo'],
            'descripcionReporte' => $meta['descripcion'],
            'restaurante' => $configuracion,
            'logoDataUri' => $this->logoDataUri($configuracion),
            'periodoDesde' => $desde,
            'periodoHasta' => $hasta,
            'generadoEn' => Carbon::now(self::TIMEZONE),
            'generadoPor' => $request->user()?->name ?? 'Administrador',
        ]))->setPaper('a4', 'landscape');

        return $pdf->download(
            'reporte_' . $tipo . '_' . $desde->format('Ymd') . '_' . $hasta->format('Ymd') . '.pdf'
        );
    }

    private function tiposDisponibles(): array
    {
        return [
            'pedidos' => [
                'titulo' => 'Reporte de pedidos',
                'descripcion' => 'Pedidos registrados, clientes, estados y montos durante el periodo.',
                'icono' => 'bi-bag-check-fill',
            ],
            'ventas' => [
                'titulo' => 'Reporte de ventas',
                'descripcion' => 'Ventas confirmadas, ticket promedio y resumen diario de ingresos.',
                'icono' => 'bi-cash-stack',
            ],
            'entregas' => [
                'titulo' => 'Reporte de entregas',
                'descripcion' => 'Entregas completadas, distancia, tarifa y distribución económica.',
                'icono' => 'bi-bicycle',
            ],
            'personal' => [
                'titulo' => 'Reporte de actividad del personal',
                'descripcion' => 'Actividad registrada de los cocineros y del equipo de Delivery.',
                'icono' => 'bi-people-fill',
            ],
        ];
    }

    private function fechaParaFormulario(mixed $valor, string $predeterminada): string
    {
        if (!is_string($valor) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $valor)) {
            return $predeterminada;
        }

        try {
            $fecha = Carbon::createFromFormat('!Y-m-d', $valor, self::TIMEZONE);

            return $fecha && $fecha->format('Y-m-d') === $valor
                ? $valor
                : $predeterminada;
        } catch (\Throwable) {
            return $predeterminada;
        }
    }

    private function reportePedidos(Carbon $inicioUtc, Carbon $finUtc): array
    {
        $pedidos = Pedido::query()
            ->with([
                'user',
                'cocinero',
                'detallePedidos.producto',
                'asignacionDelivery.delivery',
            ])
            ->whereBetween('created_at', [$inicioUtc, $finUtc])
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $filas = $pedidos->map(function (Pedido $pedido): array {
            $productos = $pedido->detallePedidos
                ->map(function ($detalle): string {
                    $nombre = $detalle->producto?->nombre ?? 'Producto eliminado';

                    return $detalle->cantidad . ' × ' . $nombre;
                })
                ->implode(', ');

            return [
                'pedido' => '#' . $pedido->id,
                'fecha' => $this->fechaTexto($pedido->created_at),
                'cliente' => $pedido->user?->name ?? 'Cliente no disponible',
                'correo' => $pedido->user?->email ?? '—',
                'cocinero' => $pedido->cocinero?->name ?? '—',
                'delivery' => $pedido->asignacionDelivery?->delivery?->name ?? 'Sin asignar',
                'productos' => $productos !== '' ? $productos : 'Sin detalle',
                'estado' => $this->estadoTexto($pedido->estado),
                'total' => (float) $pedido->total,
            ];
        })->all();

        $ventasConfirmadas = $pedidos
            ->whereIn('estado', self::ESTADOS_VENTA)
            ->sum(fn (Pedido $pedido) => (float) $pedido->total);

        return [
            'resumen' => [
                $this->indicador('Pedidos registrados', $pedidos->count()),
                $this->indicador('Pedidos entregados', $pedidos->where('estado', 'entregado')->count()),
                $this->indicador('Pedidos cancelados', $pedidos->where('estado', 'cancelado')->count()),
                $this->indicador('Importe confirmado', $this->dinero($ventasConfirmadas)),
            ],
            'filas' => $filas,
            'ventasPorDia' => [],
            'cocineros' => [],
            'deliverys' => [],
            'notaReporte' => 'El importe confirmado excluye pedidos cancelados y pedidos cuyo pago todavía no ha sido aprobado.',
        ];
    }

    private function reporteVentas(Carbon $inicioUtc, Carbon $finUtc): array
    {
        $pedidos = Pedido::query()
            ->with('user')
            ->whereBetween('created_at', [$inicioUtc, $finUtc])
            ->whereIn('estado', self::ESTADOS_VENTA)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $totalVentas = $pedidos->sum(fn (Pedido $pedido) => (float) $pedido->total);
        $cantidadVentas = $pedidos->count();
        $ticketPromedio = $cantidadVentas > 0 ? $totalVentas / $cantidadVentas : 0;

        $ventasPorDia = $pedidos
            ->groupBy(fn (Pedido $pedido) => $this->fechaClave($pedido->created_at))
            ->map(function ($grupo, string $fecha): array {
                return [
                    'fecha' => Carbon::createFromFormat('Y-m-d', $fecha, self::TIMEZONE)->format('d/m/Y'),
                    'pedidos' => $grupo->count(),
                    'total' => $grupo->sum(fn (Pedido $pedido) => (float) $pedido->total),
                ];
            })
            ->values()
            ->all();

        $filas = $pedidos->map(fn (Pedido $pedido): array => [
            'pedido' => '#' . $pedido->id,
            'fecha' => $this->fechaTexto($pedido->created_at),
            'cliente' => $pedido->user?->name ?? 'Cliente no disponible',
            'correo' => $pedido->user?->email ?? '—',
            'estado' => $this->estadoTexto($pedido->estado),
            'total' => (float) $pedido->total,
        ])->all();

        return [
            'resumen' => [
                $this->indicador('Ventas confirmadas', $cantidadVentas),
                $this->indicador('Ingresos del periodo', $this->dinero($totalVentas)),
                $this->indicador('Ticket promedio', $this->dinero($ticketPromedio)),
                $this->indicador('Días con ventas', count($ventasPorDia)),
            ],
            'filas' => $filas,
            'ventasPorDia' => $ventasPorDia,
            'cocineros' => [],
            'deliverys' => [],
            'notaReporte' => 'Se consideran pedidos con pago aprobado y que no están cancelados. Los pedidos pendientes de revisión de comprobante no se contabilizan como ventas.',
        ];
    }

    private function reporteEntregas(Carbon $inicioUtc, Carbon $finUtc): array
    {
        $asignaciones = AsignacionDelivery::query()
            ->with([
                'delivery',
                'pedido.user',
            ])
            ->whereHas('pedido', function ($query) {
                $query->where('estado', 'entregado');
            })
            ->whereRaw(
                'COALESCE(asignaciones_delivery.fecha_entrega, asignaciones_delivery.updated_at) BETWEEN ? AND ?',
                [$inicioUtc, $finUtc]
            )
            ->orderByRaw('COALESCE(asignaciones_delivery.fecha_entrega, asignaciones_delivery.updated_at) ASC')
            ->get();

        $totalDistancia = $asignaciones->sum(
            fn (AsignacionDelivery $asignacion) => (float) ($asignacion->pedido?->distancia_delivery_km ?? 0)
        );
        $totalTarifas = $asignaciones->sum(
            fn (AsignacionDelivery $asignacion) => (float) ($asignacion->pedido?->tarifa_delivery ?? 0)
        );
        $totalComisiones = $asignaciones->sum(
            fn (AsignacionDelivery $asignacion) => (float) ($asignacion->pedido?->monto_delivery ?? 0)
        );
        $totalRestaurante = $asignaciones->sum(
            fn (AsignacionDelivery $asignacion) => (float) ($asignacion->pedido?->monto_restaurante_delivery ?? 0)
        );

        $filas = $asignaciones->map(function (AsignacionDelivery $asignacion): array {
            $pedido = $asignacion->pedido;

            return [
                'pedido' => '#' . ($pedido?->id ?? $asignacion->pedido_id),
                'fecha' => $this->fechaTexto($asignacion->fecha_entrega ?? $asignacion->updated_at),
                'cliente' => $pedido?->user?->name ?? 'Cliente no disponible',
                'delivery' => $asignacion->delivery?->name ?? 'Delivery no disponible',
                'distancia' => (float) ($pedido?->distancia_delivery_km ?? 0),
                'tarifa' => (float) ($pedido?->tarifa_delivery ?? 0),
                'comision' => (float) ($pedido?->monto_delivery ?? 0),
                'restaurante' => (float) ($pedido?->monto_restaurante_delivery ?? 0),
            ];
        })->all();

        return [
            'resumen' => [
                $this->indicador('Entregas completadas', $asignaciones->count()),
                $this->indicador('Distancia acumulada', number_format($totalDistancia, 2, ',', '.') . ' km'),
                $this->indicador('Tarifas de entrega', $this->dinero($totalTarifas)),
                $this->indicador('Comisión Delivery', $this->dinero($totalComisiones)),
                $this->indicador('Parte del restaurante', $this->dinero($totalRestaurante)),
            ],
            'filas' => $filas,
            'ventasPorDia' => [],
            'cocineros' => [],
            'deliverys' => [],
            'notaReporte' => 'La fecha de entrega usa fecha_entrega cuando existe. Para entregas anteriores a la incorporación de ese campo se utiliza la última actualización de la asignación.',
        ];
    }

    private function reportePersonal(Carbon $desde, Carbon $hasta): array
    {
        $pedidosCocina = Pedido::query()
            ->whereNotNull('cocinero_id')
            ->get();

        $cocineros = User::query()
            ->where('role_id', 4)
            ->orderBy('name')
            ->get()
            ->map(function (User $cocinero) use ($pedidosCocina, $desde, $hasta): array {
                $pedidos = $pedidosCocina->where('cocinero_id', $cocinero->id);

                $iniciados = $pedidos->filter(function (Pedido $pedido) use ($desde, $hasta): bool {
                    return $this->enRango(
                        $pedido->fecha_inicio_preparacion ?? $pedido->created_at,
                        $desde,
                        $hasta
                    );
                })->count();

                $listos = $pedidos->filter(function (Pedido $pedido) use ($desde, $hasta): bool {
                    $fechaListo = $pedido->fecha_listo;

                    if (!$fechaListo && in_array($pedido->estado, [
                        'listo',
                        'asignado',
                        'en_camino',
                        'entregado',
                    ], true)) {
                        // Compatibilidad con pedidos históricos sin marca de fecha_listo.
                        $fechaListo = $pedido->created_at;
                    }

                    return $this->enRango($fechaListo, $desde, $hasta);
                })->count();

                $preparandoActual = $pedidos->where('estado', 'preparando')->count();

                return [
                    'nombre' => $cocinero->name,
                    'correo' => $cocinero->email,
                    'estado' => $cocinero->estado,
                    'iniciados' => $iniciados,
                    'listos' => $listos,
                    'preparando' => $preparandoActual,
                ];
            })
            ->all();

        $asignaciones = AsignacionDelivery::query()
            ->with('pedido')
            ->get();

        $deliverys = User::query()
            ->where('role_id', 3)
            ->orderBy('name')
            ->get()
            ->map(function (User $delivery) use ($asignaciones, $desde, $hasta): array {
                $asignacionesUsuario = $asignaciones->where('delivery_id', $delivery->id);

                $asignados = $asignacionesUsuario->filter(
                    fn (AsignacionDelivery $asignacion) => $this->enRango(
                        $asignacion->created_at,
                        $desde,
                        $hasta
                    )
                )->count();

                $iniciados = $asignacionesUsuario->filter(function (AsignacionDelivery $asignacion) use ($desde, $hasta): bool {
                    $fechaInicio = $asignacion->fecha_inicio;

                    // Fallback para entregas antiguas que no guardaban fecha_inicio.
                    if (
                        !$fechaInicio &&
                        $asignacion->pedido?->estado === 'en_camino'
                    ) {
                        $fechaInicio = $asignacion->updated_at;
                    }

                    return $this->enRango($fechaInicio, $desde, $hasta);
                })->count();

                $entregados = $asignacionesUsuario->filter(function (AsignacionDelivery $asignacion) use ($desde, $hasta): bool {
                    if ($asignacion->pedido?->estado !== 'entregado') {
                        return false;
                    }

                    return $this->enRango(
                        $asignacion->fecha_entrega ?? $asignacion->updated_at,
                        $desde,
                        $hasta
                    );
                });

                $activos = $asignacionesUsuario->filter(
                    fn (AsignacionDelivery $asignacion) => in_array(
                        $asignacion->pedido?->estado,
                        ['asignado', 'en_camino'],
                        true
                    )
                )->count();

                return [
                    'nombre' => $delivery->name,
                    'correo' => $delivery->email,
                    'estado' => $delivery->estado,
                    'asignados' => $asignados,
                    'iniciados' => $iniciados,
                    'entregados' => $entregados->count(),
                    'activos' => $activos,
                    'distancia' => $entregados->sum(
                        fn (AsignacionDelivery $asignacion) => (float) ($asignacion->pedido?->distancia_delivery_km ?? 0)
                    ),
                    'comision' => $entregados->sum(
                        fn (AsignacionDelivery $asignacion) => (float) ($asignacion->pedido?->monto_delivery ?? 0)
                    ),
                ];
            })
            ->all();

        $totalIniciadosCocina = array_sum(array_column($cocineros, 'iniciados'));
        $totalListosCocina = array_sum(array_column($cocineros, 'listos'));
        $totalAsignadosDelivery = array_sum(array_column($deliverys, 'asignados'));
        $totalEntregados = array_sum(array_column($deliverys, 'entregados'));
        $totalComision = array_sum(array_column($deliverys, 'comision'));

        return [
            'resumen' => [
                $this->indicador('Cocineros registrados', count($cocineros)),
                $this->indicador('Preparaciones iniciadas', $totalIniciadosCocina),
                $this->indicador('Pedidos listos', $totalListosCocina),
                $this->indicador('Delivery registrados', count($deliverys)),
                $this->indicador('Asignaciones realizadas', $totalAsignadosDelivery),
                $this->indicador('Entregas completadas', $totalEntregados),
                $this->indicador('Comisiones Delivery', $this->dinero($totalComision)),
            ],
            'filas' => [],
            'ventasPorDia' => [],
            'cocineros' => $cocineros,
            'deliverys' => $deliverys,
            'notaReporte' => 'Para pedidos antiguos sin marcas de tiempo de cocina se usa la fecha de creación del pedido; para asignaciones antiguas, la fecha de asignación y la última actualización disponibles.',
        ];
    }

    private function enRango(mixed $fecha, Carbon $desde, Carbon $hasta): bool
    {
        if (!$fecha) {
            return false;
        }

        try {
            $fechaLocal = Carbon::parse($fecha)->timezone(self::TIMEZONE);
        } catch (\Throwable) {
            return false;
        }

        return $fechaLocal->greaterThanOrEqualTo($desde->copy()->startOfDay())
            && $fechaLocal->lessThanOrEqualTo($hasta->copy()->endOfDay());
    }

    private function fechaClave(mixed $fecha): string
    {
        return Carbon::parse($fecha)
            ->timezone(self::TIMEZONE)
            ->format('Y-m-d');
    }

    private function fechaTexto(mixed $fecha): string
    {
        return $fecha
            ? Carbon::parse($fecha)->timezone(self::TIMEZONE)->format('d/m/Y H:i')
            : '—';
    }

    private function estadoTexto(?string $estado): string
    {
        return [
            'pendiente' => 'Pendiente',
            'comprobante_enviado' => 'Comprobante enviado',
            'pagado' => 'Pagado',
            'preparando' => 'Preparando',
            'listo' => 'Listo',
            'asignado' => 'Asignado a Delivery',
            'en_camino' => 'En camino',
            'entregado' => 'Entregado',
            'cancelado' => 'Cancelado',
        ][$estado ?? ''] ?? ucfirst(str_replace('_', ' ', $estado ?? 'Sin estado'));
    }

    private function indicador(string $etiqueta, int|string $valor): array
    {
        return [
            'etiqueta' => $etiqueta,
            'valor' => (string) $valor,
        ];
    }

    private function dinero(float|int $cantidad): string
    {
        return 'Bs ' . number_format((float) $cantidad, 2, ',', '.');
    }

    private function logoDataUri(?Configuracion $configuracion): ?string
    {
        $ruta = $configuracion?->logo;

        if (!$ruta || preg_match('/^https?:\/\//i', $ruta)) {
            return null;
        }

        $disk = Storage::disk('public');

        if (!$disk->exists($ruta)) {
            return null;
        }

        $mime = $disk->mimeType($ruta) ?: 'image/png';

        if (!in_array($mime, ['image/png', 'image/jpeg', 'image/gif'], true)) {
            return null;
        }

        return 'data:' . $mime . ';base64,' . base64_encode($disk->get($ruta));
    }
}
