<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;

class ConfiguracionController extends Controller
{
    /**
     * Mostrar la configuración general del restaurante,
     * su ubicación y la configuración económica de Delivery.
     */
    public function index(): View
    {
        $configuracion = Configuracion::first();

        return view(
            'admin.configuracion.index',
            compact('configuracion')
        );
    }

    /**
     * Guardar la información general y los archivos del restaurante.
     *
     * La dirección queda fuera de este formulario porque su guardado
     * pertenece exclusivamente a la sección de ubicación del mapa.
     */
    public function update(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'nombre_restaurante' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'qr_pago' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $configuracion = Configuracion::first();

        if (!$configuracion) {
            $configuracion = new Configuracion();
        }

        $configuracion->nombre_restaurante = $datos['nombre_restaurante'];
        $configuracion->telefono = $datos['telefono'] ?? null;

        if ($request->hasFile('logo')) {
            if ($configuracion->logo) {
                Storage::disk('public')->delete($configuracion->logo);
            }

            $configuracion->logo = $request->file('logo')->store(
                'logo',
                'public'
            );
        }

        if ($request->hasFile('qr_pago')) {
            if ($configuracion->qr_pago) {
                Storage::disk('public')->delete($configuracion->qr_pago);
            }

            $configuracion->qr_pago = $request->file('qr_pago')->store(
                'qr',
                'public'
            );
        }

        $configuracion->save();

        return back()->with(
            'success',
            'Información del restaurante actualizada correctamente.'
        );
    }

    /**
     * Convertir las coordenadas del marcador del restaurante en una dirección
     * legible usando OpenStreetMap/Nominatim.
     */
    public function obtenerDireccionUbicacion(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'latitud' => 'required|numeric|between:-90,90',
            'longitud' => 'required|numeric|between:-180,180',
        ]);

        $latitud = round((float) $datos['latitud'], 7);
        $longitud = round((float) $datos['longitud'], 7);
        $cacheKey = 'nominatim_reverse_restaurante_' . md5($latitud . ',' . $longitud);

        $direccion = Cache::remember(
            $cacheKey,
            now()->addMinutes(10),
            function () use ($latitud, $longitud) {
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
            }
        );

        if (!$direccion) {
            return response()->json([
                'ok' => false,
                'message' => 'No se pudo convertir la ubicación en una dirección.',
            ], 422);
        }

        return response()->json([
            'ok' => true,
            'direccion' => $direccion,
        ]);
    }

    /**
     * Guardar dirección + latitud + longitud del restaurante.
     *
     * Esta acción es independiente del resto de la configuración.
     */
    public function updateUbicacion(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'direccion' => 'required|string|max:1000',
            'latitud_restaurante' => 'required|numeric|between:-90,90',
            'longitud_restaurante' => 'required|numeric|between:-180,180',
        ]);

        $configuracion = Configuracion::first();

        if (!$configuracion) {
            $configuracion = new Configuracion([
                'nombre_restaurante' => 'Sabor Express',
            ]);
        }

        $configuracion->direccion = trim($datos['direccion']);
        $configuracion->latitud_restaurante = round(
            (float) $datos['latitud_restaurante'],
            7
        );
        $configuracion->longitud_restaurante = round(
            (float) $datos['longitud_restaurante'],
            7
        );

        $configuracion->save();

        return back()->with(
            'success',
            'Ubicación del restaurante guardada correctamente. Esta ubicación será el origen de los pedidos nuevos.'
        );
    }

    /**
     * Guardar la configuración económica de Delivery.
     */
    public function updateDelivery(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'tarifa_minima_delivery' => 'required|numeric|min:0|max:999999.99',
            'precio_km_delivery' => 'required|numeric|min:0|max:999999.99',
            'porcentaje_delivery' => 'required|numeric|min:0|max:100',
            'porcentaje_restaurante_delivery' => 'required|numeric|min:0|max:100',
        ]);

        $porcentajeDelivery = round(
            (float) $datos['porcentaje_delivery'],
            2
        );

        $porcentajeRestaurante = round(
            (float) $datos['porcentaje_restaurante_delivery'],
            2
        );

        if (round($porcentajeDelivery + $porcentajeRestaurante, 2) !== 100.00) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Los porcentajes de Delivery y restaurante deben sumar exactamente 100%.'
                );
        }

        $configuracion = Configuracion::first();

        if (!$configuracion) {
            $configuracion = new Configuracion([
                'nombre_restaurante' => 'Sabor Express',
            ]);
        }

        $configuracion->tarifa_minima_delivery = round(
            (float) $datos['tarifa_minima_delivery'],
            2
        );

        $configuracion->precio_km_delivery = round(
            (float) $datos['precio_km_delivery'],
            2
        );

        $configuracion->porcentaje_delivery = $porcentajeDelivery;
        $configuracion->porcentaje_restaurante_delivery = $porcentajeRestaurante;

        $configuracion->save();

        return back()->with(
            'success',
            'Configuración económica de Delivery actualizada correctamente.'
        );
    }
}
