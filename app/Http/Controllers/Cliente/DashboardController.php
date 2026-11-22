<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Mostrar el inicio del Cliente con el catálogo y la información
     * de ubicación y contacto del restaurante.
     *
     * La última ubicación del Cliente se obtiene del pedido más reciente
     * que tenga coordenadas guardadas. No se agregan campos ni tablas.
     */
    public function index(): View
    {
        $configuracion = Configuracion::query()->first();

        $productos = Producto::query()
            ->with('categoria')
            ->where('estado', 'disponible')
            ->where('stock', '>', 0)
            ->orderBy('categoria_id')
            ->orderBy('nombre')
            ->get();

        $usuario = Auth::user();
        $esCliente = $usuario?->role?->nombre === 'Cliente';

        $ultimaUbicacion = null;

        if ($esCliente) {
            $ultimaUbicacion = Pedido::query()
                ->where('user_id', $usuario->id)
                ->whereNotNull('latitud')
                ->whereNotNull('longitud')
                ->latest('id')
                ->first([
                    'id',
                    'latitud',
                    'longitud',
                    'direccion_entrega',
                    'created_at',
                ]);
        }

        // WhatsApp exige el número internacional sin espacios ni símbolos.
        // Los números locales bolivianos de 8 dígitos reciben el prefijo 591.
        $telefonoWhatsApp = preg_replace(
            '/\D+/',
            '',
            (string) ($configuracion?->telefono ?? '')
        );

        if (str_starts_with($telefonoWhatsApp, '00')) {
            $telefonoWhatsApp = substr($telefonoWhatsApp, 2);
        }

        if (strlen($telefonoWhatsApp) === 8) {
            $telefonoWhatsApp = '591' . $telefonoWhatsApp;
        } elseif (
            strlen($telefonoWhatsApp) === 9
            && str_starts_with($telefonoWhatsApp, '0')
        ) {
            $telefonoWhatsApp = '591' . substr($telefonoWhatsApp, 1);
        }

        $enlaceWhatsApp = strlen($telefonoWhatsApp) >= 11
            ? 'https://wa.me/' . $telefonoWhatsApp . '?text=' . rawurlencode(
                'Hola, Sabor Express. Quisiera realizar un pedido.'
            )
            : null;

        return view('cliente.dashboard.index', compact(
            'configuracion',
            'productos',
            'ultimaUbicacion',
            'esCliente',
            'enlaceWhatsApp'
        ));
    }
}
