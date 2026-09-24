<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CocineroController extends Controller
{
    /**
     * Mostrar los cocineros registrados.
     *
     * La búsqueda se realiza únicamente por correo electrónico.
     */
    public function index(Request $request)
    {
        $buscar = trim((string) $request->input('buscar', ''));

        $cocineros = User::where('role_id', 4)
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where('email', 'like', '%' . $buscar . '%');
            })
            ->orderBy('name')
            ->get();

        if ($request->expectsJson()) {
            return response()->json([
                'html' => view(
                    'admin.cocineros._tabla',
                    compact('cocineros')
                )->render(),
                'count' => $cocineros->count(),
            ]);
        }

        return view(
            'admin.cocineros.index',
            compact('cocineros')
        );
    }

    /**
     * Mostrar información del cocinero.
     */
    public function show($id)
    {
        $cocinero = User::where('role_id', 4)
            ->withCount('pedidosCocina')
            ->with([
                'pedidosCocina' => fn ($query) => $query
                    ->with('user')
                    ->latest()
                    ->take(10),
            ])
            ->findOrFail($id);

        return view(
            'admin.cocineros.show',
            compact('cocinero')
        );
    }

    /**
     * Mostrar formulario para crear un cocinero.
     */
    public function create()
    {
        return view('admin.cocineros.create');
    }

    /**
     * Guardar un nuevo cocinero.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],

            'estado' => [
                'required',
                Rule::in(['activo', 'inactivo']),
            ],
        ]);

        User::create([
            'role_id' => 4,
            'name' => $request->name,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'password' => Hash::make($request->password),
            'estado' => $request->estado,
        ]);

        return redirect()
            ->route('admin.cocineros.index')
            ->with(
                'success',
                'Cocinero creado correctamente.'
            );
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit($id)
    {
        $cocinero = User::where('role_id', 4)
            ->findOrFail($id);

        return view(
            'admin.cocineros.edit',
            compact('cocinero')
        );
    }

    /**
     * Actualizar un cocinero.
     */
    public function update(Request $request, $id)
    {
        $cocinero = User::where('role_id', 4)
            ->findOrFail($id);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . $cocinero->id,
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],

            'password' => [
                'nullable',
                'string',
                'min:6',
                'confirmed',
            ],

            'estado' => [
                'required',
                Rule::in(['activo', 'inactivo']),
            ],
        ]);

        $cocinero->name = $request->name;
        $cocinero->email = $request->email;
        $cocinero->telefono = $request->telefono;
        $cocinero->estado = $request->estado;

        if ($request->filled('password')) {
            $cocinero->password = Hash::make(
                $request->password
            );
        }

        $cocinero->save();

        return redirect()
            ->route('admin.cocineros.index')
            ->with(
                'success',
                'Cocinero actualizado correctamente.'
            );
    }

    /**
     * Desactivar cocinero.
     *
     * Se conserva el registro para no perder el historial de trabajo.
     */
    public function destroy($id)
    {
        $cocinero = User::where('role_id', 4)
            ->findOrFail($id);

        $cocinero->estado = 'inactivo';
        $cocinero->save();

        return back()->with(
            'success',
            'Cocinero desactivado correctamente.'
        );
    }

    /**
     * Activar cocinero nuevamente.
     */
    public function activar($id)
    {
        $cocinero = User::where('role_id', 4)
            ->findOrFail($id);

        $cocinero->estado = 'activo';
        $cocinero->save();

        return back()->with(
            'success',
            'Cocinero activado correctamente.'
        );
    }
}
