<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pedido;
use App\Models\AsignacionDelivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DeliveryController extends Controller
{
    /**
     * Lista de repartidores
     */
    public function index()
    {
        $deliverys = User::where('role_id', 3)
            ->withCount('asignacionesDelivery')
            ->orderBy('id', 'desc')
            ->get();

        return view(
            'admin.deliverys.index',
            compact('deliverys')
        );
    }


    /**
     * Mostrar formulario para crear delivery
     */
    public function create()
    {
        return view('admin.deliverys.create');
    }


    /**
     * Guardar nuevo delivery
     */
    public function store(Request $request)
    {
        $request->validate([

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email'
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:20'
            ],

            'direccion' => [
                'nullable',
                'string'
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed'
            ],

            'estado' => [
                'required',
                Rule::in(['activo', 'inactivo'])
            ]

        ]);


        User::create([

            'role_id' => 3,

            'name' => $request->name,

            'email' => $request->email,

            'telefono' => $request->telefono,

            'direccion' => $request->direccion,

            'password' => Hash::make($request->password),

            'estado' => $request->estado

        ]);


        return redirect()

            ->route('admin.deliverys.index')

            ->with(
                'success',
                'Repartidor creado correctamente.'
            );
    }


    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $delivery = User::where('role_id', 3)
            ->findOrFail($id);

        return view(
            'admin.deliverys.edit',
            compact('delivery')
        );
    }


    /**
     * Actualizar delivery
     */
    public function update(Request $request, $id)
    {
        $delivery = User::where('role_id', 3)
            ->findOrFail($id);


        $request->validate([

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $delivery->id
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:20'
            ],

            'direccion' => [
                'nullable',
                'string'
            ],

            'password' => [
                'nullable',
                'string',
                'min:6',
                'confirmed'
            ],

            'estado' => [
                'required',
                Rule::in(['activo', 'inactivo'])
            ]

        ]);


        $delivery->name = $request->name;

        $delivery->email = $request->email;

        $delivery->telefono = $request->telefono;

        $delivery->direccion = $request->direccion;

        $delivery->estado = $request->estado;


        /*
        |--------------------------------------------------------------------------
        | Solo cambiar contraseña si se escribió una nueva
        |--------------------------------------------------------------------------
        */

        if ($request->filled('password')) {

            $delivery->password = Hash::make(
                $request->password
            );
        }


        $delivery->save();


        return redirect()

            ->route('admin.deliverys.index')

            ->with(
                'success',
                'Repartidor actualizado correctamente.'
            );
    }


    /**
     * Desactivar delivery
     */
    public function destroy($id)
    {
        $delivery = User::where('role_id', 3)
            ->findOrFail($id);


        $delivery->estado = 'inactivo';

        $delivery->save();


        return back()->with(
            'success',
            'Repartidor desactivado correctamente.'
        );
    }


    /**
     * Activar delivery nuevamente
     */
    public function activar($id)
    {
        $delivery = User::where('role_id', 3)
            ->findOrFail($id);


        $delivery->estado = 'activo';

        $delivery->save();


        return back()->with(
            'success',
            'Repartidor activado correctamente.'
        );
    }


    /**
     * Asignar delivery a un pedido
     */
    public function asignar(Request $request, $pedido_id)
    {
        $request->validate([

            'delivery_id' => [
                'required',
                Rule::exists('users', 'id')
                    ->where(function ($query) {

                        $query->where('role_id', 3)
                            ->where('estado', 'activo');
                    })
            ]

        ]);


        $pedido = Pedido::findOrFail($pedido_id);


        AsignacionDelivery::updateOrCreate(

            [
                'pedido_id' => $pedido_id,
            ],

            [
                'delivery_id' => $request->delivery_id,

                'estado' => 'pendiente',

                'fecha_asignacion' => now(),

            ]

        );


        $pedido->estado = 'asignado';

        $pedido->save();


        return back()->with(
            'success',
            'Delivery asignado correctamente.'
        );
    }
}
