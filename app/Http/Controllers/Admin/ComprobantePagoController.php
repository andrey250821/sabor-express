<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComprobantePago;
use App\Models\Pedido;
use App\Models\Notificacion;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ComprobantePagoController extends Controller
{
    public function index($estado = 'en_revision')
    {
        $estadosPermitidos = [
            'en_revision',
            'aprobado',
            'rechazado',
        ];

        if (!in_array($estado, $estadosPermitidos, true)) {
            abort(404);
        }

        $comprobantes = ComprobantePago::with([
            'pedido.user',
        ])
            ->where('estado', $estado)
            ->get();

        $enRevision = ComprobantePago::where(
            'estado',
            'en_revision'
        )->count();

        $aprobados = ComprobantePago::where(
            'estado',
            'aprobado'
        )->count();

        $rechazados = ComprobantePago::where(
            'estado',
            'rechazado'
        )->count();

        return view('admin.comprobantes.index', compact(
            'comprobantes',
            'enRevision',
            'aprobados',
            'rechazados',
            'estado'
        ));
    }

    /**
     * Mostrar la imagen del comprobante al administrador.
     */
    public function verImagen(int $id)
    {
        $comprobante = ComprobantePago::findOrFail($id);

        if (!$comprobante->imagen) {
            abort(404);
        }

        $disk = Storage::disk('public');

        if (!$disk->exists($comprobante->imagen)) {
            abort(404);
        }

        return response()->file($disk->path($comprobante->imagen), [
            'Content-Type' => $disk->mimeType($comprobante->imagen) ?: 'application/octet-stream',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    public function aprobar($id)
    {
        $comprobante = ComprobantePago::findOrFail($id);

        if ($comprobante->estado !== 'en_revision') {
            return back()->with(
                'error',
                'Este comprobante no requiere revisión manual.'
            );
        }

        $comprobante->update([
            'estado' => 'aprobado',
            'fecha_revision' => Carbon::now(),
        ]);

        $pedido = $comprobante->pedido;

        $pedido->update([
            'estado' => 'pagado',
        ]);

        Notificacion::create([
            'user_id' => $pedido->user_id,
            'pedido_id' => $pedido->id,
            'mensaje' => 'Tu pedido fue aceptado y está pendiente de preparación',
            'tipo' => 'cliente',
            'evento' => 'pedido_aceptado',
            'leido' => false,
        ]);

        return back()->with(
            'success',
            'Comprobante aprobado correctamente'
        );
    }

    public function rechazar($id)
    {
        $comprobante = ComprobantePago::findOrFail($id);

        if ($comprobante->estado !== 'en_revision') {
            return back()->with(
                'error',
                'Este comprobante no requiere revisión manual.'
            );
        }

        DB::transaction(function () use ($comprobante) {
            $pedido = Pedido::with('detallePedidos')
                ->findOrFail($comprobante->pedido_id);

            /*
             * El stock fue reservado cuando el cliente creó el pedido.
             * Al rechazar el comprobante, devolvemos exactamente las
             * unidades que pertenecían a ese pedido.
             *
             * Cada producto se bloquea mientras se actualiza para evitar
             * inconsistencias si otro pedido modifica el mismo stock.
             */
            foreach ($pedido->detallePedidos as $detalle) {
                $producto = Producto::where('id', $detalle->producto_id)
                    ->lockForUpdate()
                    ->first();

                if (!$producto) {
                    continue;
                }

                $producto->stock += (int) $detalle->cantidad;

                $producto->estado = $producto->stock > 0
                    ? 'disponible'
                    : 'agotado';

                $producto->save();
            }

            $comprobante->update([
                'estado' => 'rechazado',
                'fecha_revision' => Carbon::now(),
            ]);

            $pedido->update([
                'estado' => 'cancelado',
            ]);

            Notificacion::create([
                'user_id' => $pedido->user_id,
                'pedido_id' => $pedido->id,
                'mensaje' => 'Tu pedido #' . $pedido->id . ' fue rechazado porque el comprobante de pago no pudo validarse. Puedes realizar un nuevo pedido con un comprobante válido.',
                'tipo' => 'cliente',
                'evento' => 'comprobante_rechazado',
                'leido' => false,
            ]);
        });

        return back()->with(
            'success',
            'Comprobante rechazado correctamente y stock liberado.'
        );
    }
}
