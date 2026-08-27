<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Configuracion;
use Illuminate\Support\Facades\Storage;


class ConfiguracionController extends Controller
{


    public function index()
    {

        $configuracion = Configuracion::first();


        return view(
            'admin.configuracion.index',
            compact('configuracion')
        );

    }





    public function update(Request $request)
    {


        $request->validate([

            'nombre_restaurante'=>'required',

            'telefono'=>'nullable',

            'direccion'=>'nullable',


            'qr_pago'=>'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'logo'=>'nullable|image|mimes:jpg,jpeg,png|max:2048'

        ]);





        $configuracion = Configuracion::first();



        if(!$configuracion)
        {

            $configuracion = new Configuracion();

        }




        $configuracion->nombre_restaurante =
            $request->nombre_restaurante;


        $configuracion->telefono =
            $request->telefono;


        $configuracion->direccion =
            $request->direccion;






        /*
        ==================================
        ACTUALIZAR LOGO
        ==================================
        */


        if($request->hasFile('logo'))
        {


            // eliminar logo anterior

            if($configuracion->logo)
            {

                Storage::disk('public')
                ->delete($configuracion->logo);

            }



            // guardar nuevo logo

            $logo = $request->file('logo')
                    ->store('logo','public');



            $configuracion->logo = $logo;


        }






        /*
        ==================================
        ACTUALIZAR QR
        ==================================
        */


        if($request->hasFile('qr_pago'))
        {


            // eliminar QR anterior

            if($configuracion->qr_pago)
            {

                Storage::disk('public')
                ->delete($configuracion->qr_pago);

            }



            // guardar nuevo QR


            $qr = $request->file('qr_pago')
                    ->store('qr','public');



            $configuracion->qr_pago = $qr;


        }





        $configuracion->save();





        return back()->with(
            'success',
            'Configuración actualizada correctamente'
        );


    }


}