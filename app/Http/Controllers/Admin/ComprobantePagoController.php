<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComprobantePago;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Carbon\Carbon;


class ComprobantePagoController extends Controller
{


    public function index($estado = 'pendiente')
    {

        $comprobantes = ComprobantePago::with([
            'pedido.user'
        ])
            ->where('estado', $estado)
            ->get();


        $pendientes = ComprobantePago::where('estado', 'pendiente')->count();

        $aprobados = ComprobantePago::where('estado', 'aprobado')->count();

        $rechazados = ComprobantePago::where('estado', 'rechazado')->count();


        return view('admin.comprobantes.index', compact(
            'comprobantes',
            'pendientes',
            'aprobados',
            'rechazados',
            'estado'
        ));
    }





    public function aprobar($id)
    {


        $comprobante = ComprobantePago::findOrFail($id);



        $comprobante->update([

            'estado' => 'aprobado',

            'fecha_revision' => Carbon::now()

        ]);



        $pedido = $comprobante->pedido;



        $pedido->update([

            'estado' => 'pagado'
        ]);




        Notificacion::create([

            'user_id' => $pedido->user_id,

            'pedido_id' => $pedido->id,

            'mensaje' => 'Tu pedido fue aceptado y está pendiente de preparación',

            'tipo' => 'cliente',

            'evento' => 'pedido_aceptado',

            'leido' => false

        ]);




        return back()->with(
            'success',
            'Comprobante aprobado correctamente'
        );
    }







    public function rechazar($id)
    {


        $comprobante = ComprobantePago::findOrFail($id);



        $comprobante->update([

            'estado' => 'rechazado',

            'fecha_revision' => Carbon::now()

        ]);



        $pedido = $comprobante->pedido;



        $pedido->update([

            'estado' => 'cancelado'

        ]);




        Notificacion::create([

            'user_id' => $pedido->user_id,

            'pedido_id' => $pedido->id,

            'mensaje' => 'Tu comprobante fue rechazado. Por favor envía uno nuevo.',

            'tipo' => 'cliente',

            'evento' => 'comprobante_rechazado',

            'leido' => false

        ]);





        return back()->with(

            'success',

            'Comprobante rechazado correctamente'

        );
    }
}
