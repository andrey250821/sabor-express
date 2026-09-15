<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Support\Facades\Storage;
use App\Models\Calificacion;

class ProductoController extends Controller
{


    public function index(Request $request)
    {
        $categorias = Categoria::orderBy('nombre')->get();

        $query = Producto::with('categoria');

        if ($request->filled('categoria_id')) {

            $query->where(
                'categoria_id',
                $request->categoria_id
            );
        }

        $productos = $query
            ->orderBy('nombre')
            ->get();

        return view(
            'admin.productos.index',
            compact('productos', 'categorias')
        );
    }




    public function create()
    {

        $categorias = Categoria::all();


        return view(
            'admin.productos.create',
            compact('categorias')
        );
    }






    public function store(Request $request)
    {
        $request->validate([

            'categoria_id' => 'required|exists:categorias,id',

            'nombre' => 'required|string|max:255',

            'descripcion' => 'nullable|string',

            'precio' => 'required|numeric|min:0',

            'stock' => 'required|integer|min:0',

            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

        ]);


        $datos = $request->only([

            'categoria_id',
            'nombre',
            'descripcion',
            'precio',
            'stock'

        ]);


        $datos['estado'] = 'disponible';


        if ($request->hasFile('imagen')) {

            $datos['imagen'] =
                $request->file('imagen')->store(
                    'productos',
                    'public'
                );
        }


        Producto::create($datos);


        return redirect()
            ->route('admin.productos.index')
            ->with(
                'success',
                'Producto creado correctamente.'
            );
    }






    public function edit($id)
    {


        $producto = Producto::findOrFail($id);


        $categorias = Categoria::all();



        return view(
            'admin.productos.edit',
            compact(
                'producto',
                'categorias'
            )
        );
    }







    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        // Validación
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'estado' => 'required|in:disponible,agotado',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        // Datos del producto
        $datos = $request->only([
            'categoria_id',
            'nombre',
            'descripcion',
            'precio',
            'stock',
            'estado'
        ]);

        // ==========================================
        // CAMBIO DE IMAGEN
        // ==========================================

        if ($request->hasFile('imagen')) {

            // Guardar la imagen anterior
            $imagenAnterior = $producto->imagen;

            // Guardar la nueva imagen
            $nuevaImagen = $request->file('imagen')
                ->store('productos', 'public');

            // Asignar la nueva ruta
            $datos['imagen'] = $nuevaImagen;

            // Actualizar producto
            $producto->update($datos);

            // Eliminar imagen anterior
            if (
                $imagenAnterior &&
                Storage::disk('public')->exists($imagenAnterior)
            ) {
                Storage::disk('public')->delete($imagenAnterior);
            }
        } else {

            // No se seleccionó una imagen nueva.
            // Se conserva la imagen actual.
            $producto->update($datos);
        }

        return redirect()
            ->route('admin.productos.index')
            ->with(
                'success',
                'Producto actualizado correctamente.'
            );
    }

    /**
     * Mostrar las calificaciones de un producto.
     */
    public function calificaciones($id)
    {
        $producto = Producto::with('categoria')
            ->findOrFail($id);

        $calificaciones = Calificacion::where(
            'producto_id',
            $producto->id
        )
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $promedio = $calificaciones->avg('puntuacion');

        $totalCalificaciones = $calificaciones->count();

        $cantidadEstrellas = [
            5 => $calificaciones->where('puntuacion', 5)->count(),
            4 => $calificaciones->where('puntuacion', 4)->count(),
            3 => $calificaciones->where('puntuacion', 3)->count(),
            2 => $calificaciones->where('puntuacion', 2)->count(),
            1 => $calificaciones->where('puntuacion', 1)->count(),
        ];

        return view(
            'admin.productos.calificaciones',
            compact(
                'producto',
                'calificaciones',
                'promedio',
                'totalCalificaciones',
                'cantidadEstrellas'
            )
        );
    }
    public function destroy($id)
    {


        $producto = Producto::findOrFail($id);



        if ($producto->imagen) {

            Storage::disk('public')
                ->delete($producto->imagen);
        }



        $producto->delete();



        return back()
            ->with(
                'success',
                'Producto eliminado correctamente'
            );
    }
}
