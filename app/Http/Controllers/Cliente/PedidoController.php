<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Notificacion;
use App\Models\User;
use App\Models\ComprobantePago;
use App\Models\Configuracion;
use App\Models\DetallePedido;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Services\ValidarComprobantePagoService;
use App\Services\GenerarComprobanteOcrService;

class PedidoController extends Controller
{
    public function create()
    {
        $carrito = session()->get('carrito', []);

        if (count($carrito) === 0) {
            return redirect()
                ->route('cliente.productos.index')
                ->with('error', 'El carrito está vacío');
        }

        $total = 0;

        foreach ($carrito as &$producto) {

            $producto['subtotal'] =
                $producto['cantidad'] * $producto['precio'];

            $total += $producto['subtotal'];
        }

        unset($producto);

        session()->put('carrito', $carrito);

        $configuracion = Configuracion::first();

        return view('cliente.pedidos.create', compact('total', 'configuracion'));
    }

    /**
     * Convierte coordenadas GPS en una dirección usando OpenStreetMap/Nominatim.
     * Se hace desde Laravel para evitar depender de Google Geocoding y de CORS.
     */
    public function direccion(Request $request)
    {
        $datos = $request->validate([
            'latitud' => 'required|numeric|between:-90,90',
            'longitud' => 'required|numeric|between:-180,180',
        ]);

        $latitud = round((float) $datos['latitud'], 7);
        $longitud = round((float) $datos['longitud'], 7);
        $cacheKey = 'nominatim_reverse_' . md5($latitud . ',' . $longitud);

        $direccion = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($latitud, $longitud) {
            $respuesta = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'SaborExpress/1.0 (aplicacion web de pedidos)',
                    'Accept-Language' => 'es',
                ])
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'format' => 'jsonv2',
                    'lat' => $latitud,
                    'lon' => $longitud,
                    'addressdetails' => 1,
                    'zoom' => 18,
                ]);

            if (!$respuesta->successful()) {
                return null;
            }

            $resultado = $respuesta->json();
            $address = $resultado['address'] ?? [];

            $partes = array_filter([
                $address['house_number'] ?? null,
                $address['road'] ?? null,
                $address['neighbourhood'] ?? ($address['suburb'] ?? null),
                $address['city'] ?? ($address['town'] ?? ($address['municipality'] ?? null)),
                $address['state'] ?? null,
                $address['country'] ?? null,
            ]);

            return !empty($partes)
                ? implode(', ', $partes)
                : ($resultado['display_name'] ?? null);
        });

        if (!$direccion) {
            return response()->json([
                'ok' => false,
                'message' => 'No se pudo obtener la dirección para estas coordenadas.',
            ], 422);
        }

        return response()->json([
            'ok' => true,
            'direccion' => $direccion,
        ]);
    }

    /**
     * Generar un comprobante de prueba a partir del pedido que el cliente
     * está armando en el checkout, antes de guardar el pedido en la BD.
     */
    public function generarComprobantePrueba(
        GenerarComprobanteOcrService $generador
    ) {
        $carrito = session()->get('carrito', []);

        if (count($carrito) === 0) {
            return response()->json([
                'ok' => false,
                'message' => 'El carrito está vacío.',
            ], 422);
        }

        $total = 0;

        foreach ($carrito as $item) {
            $cantidad = (int) ($item['cantidad'] ?? 0);
            $precio = (float) ($item['precio'] ?? 0);

            $total += $cantidad * $precio;
        }

        if ($total <= 0) {
            return response()->json([
                'ok' => false,
                'message' => 'No se pudo calcular el total del pedido.',
            ], 422);
        }

        try {
            $comprobante = $generador->generarParaCheckout(
                cliente: Auth::user()->name,
                monto: $total
            );

            return response()->json([
                'ok' => true,
                'archivo' => $comprobante['archivo'],
                'referencia' => $comprobante['referencia'],
                'url' => asset('storage/' . $comprobante['ruta']),
                'mensaje' => 'Comprobante de prueba generado con los datos actuales del pedido.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'No se pudo generar el comprobante de prueba.',
                'detail' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request, ValidarComprobantePagoService $validador)
    {
        $request->validate([
            'direccion_entrega' => 'required|string|max:1000',
            'referencia_delivery' => 'nullable|string|max:1000',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'observacion_cliente' => 'nullable|string|max:500',
            'comprobante' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $carrito = session()->get('carrito', []);

        if (count($carrito) === 0) {
            return redirect()
                ->route('cliente.productos.index')
                ->with('error', 'El carrito está vacío');
        }

        DB::beginTransaction();

        try {
            $total = 0;

            foreach ($carrito as &$item) {

                $item['subtotal'] =
                    $item['cantidad'] * $item['precio'];

                $total += $item['subtotal'];
            }

            unset($item);

            $pedido = Pedido::create([
                'user_id' => Auth::id(),
                'total' => $total,
                'estado' => 'comprobante_enviado',
                'latitud' => $request->latitud,
                'longitud' => $request->longitud,
                'direccion_entrega' => $request->direccion_entrega,
                'observacion_cliente' => $request->observacion_cliente,
                'referencia_delivery' => $request->referencia_delivery,
            ]);

            foreach ($carrito as $item) {
                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio'],
                    'subtotal' => $item['subtotal'],
                ]);

                $producto = Producto::find($item['id']);

                if ($producto) {
                    $producto->stock -= $item['cantidad'];

                    if ($producto->stock <= 0) {
                        $producto->stock = 0;
                        $producto->estado = 'agotado';
                    }

                    $producto->save();
                }
            }

            $imagen = $request->file('comprobante')->store('comprobantes', 'public');

            $comprobante = ComprobantePago::create([
                'pedido_id' => $pedido->id,
                'imagen' => $imagen,
                'estado' => 'en_revision',
            ]);

            DB::commit();

            // Ejecutar OCR después del commit para no mantener abierta
            // la transacción mientras se ejecuta el proceso externo.
            $validacion = $validador->validar(
                $pedido,
                storage_path('app/public/' . $imagen)
            );

            $comprobante->update([
                'estado' => $validacion['estado'],
                'referencia_bancaria' => $validacion['datos']['referencia'] ?? null,
                'motivo_revision' => $validacion['motivo_revision'],
                'datos_ocr' => $validacion['datos'],
            ]);

            if ($validacion['estado'] === 'aprobado') {
                $pedido->update([
                    'estado' => 'pagado',
                ]);

                Notificacion::create([
                    'user_id' => $pedido->user_id,
                    'pedido_id' => $pedido->id,
                    'mensaje' => 'Tu pago fue verificado correctamente y tu pedido pasó a preparación.',
                    'tipo' => 'cliente',
                    'evento' => 'pedido_aceptado',
                    'leido' => false,
                ]);
            } else {
                // Solo los comprobantes con problemas llegan al administrador.
                $administradores = User::whereHas('role', function ($query) {
                    $query->where('nombre', 'Administrador');
                })->get();

                foreach ($administradores as $administrador) {
                    Notificacion::create([
                        'user_id' => $administrador->id,
                        'pedido_id' => $pedido->id,
                        'mensaje' => 'El comprobante del pedido #' . $pedido->id . ' requiere revisión manual.',
                        'tipo' => 'administrador',
                        'evento' => 'comprobante_en_revision',
                        'leido' => false,
                    ]);
                }
            }

            session()->forget('carrito');

            return redirect()
                ->route('cliente.pedidos.show', $pedido->id)
                ->with(
                    'success',
                    $validacion['estado'] === 'aprobado'
                        ? 'Pedido enviado y pago verificado correctamente.'
                        : 'Pedido enviado. El comprobante requiere revisión manual.'
                );
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Error al procesar pedido: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $pedidos = Pedido::where('user_id', Auth::id())
            ->with(['detallePedidos.producto', 'comprobantePago'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cliente.pedidos.index', compact('pedidos'));
    }

    public function show($id)
    {
        $pedido = Pedido::where('user_id', Auth::id())
            ->with([
                'detallePedidos.producto',
                'comprobantePago',
            ])
            ->findOrFail($id);


        /*
    |--------------------------------------------------------------------------
    | CALIFICACIONES DEL USUARIO PARA LOS PRODUCTOS DEL PEDIDO
    |--------------------------------------------------------------------------
    |
    | No buscamos solamente las calificaciones pertenecientes a este pedido.
    | Buscamos las calificaciones que el usuario ya realizó para cualquiera
    | de los productos que aparecen en este pedido.
    |
    | Esto permite que:
    |
    | Pedido #1 -> compra Hamburguesa -> califica
    | Pedido #2 -> vuelve a comprar Hamburguesa -> aparece su calificación
    |
    */

        $productoIds = $pedido->detallePedidos
            ->pluck('producto_id')
            ->filter()
            ->unique();


        $calificaciones = \App\Models\Calificacion::where(
            'user_id',
            Auth::id()
        )
            ->whereIn('producto_id', $productoIds)
            ->get()
            ->keyBy('producto_id');


        return view(
            'cliente.pedidos.show',
            compact(
                'pedido',
                'calificaciones'
            )
        );
    }
}
