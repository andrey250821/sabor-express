<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Support\Facades\Storage;
use App\Models\Calificacion;
use App\Services\FechaFiltroService;

class ProductoController extends Controller
{
    /**
     * Comprobar nombres duplicados en todo el catálogo, sin importar
     * mayúsculas/minúsculas ni espacios al inicio o al final.
     */
    private function nombreYaExiste(string $nombre, ?int $excluirId = null): bool
    {
        $consulta = Producto::query()
            ->whereRaw('LOWER(TRIM(nombre)) = ?', [
                mb_strtolower(trim($nombre), 'UTF-8'),
            ]);

        if ($excluirId !== null) {
            $consulta->where('id', '!=', $excluirId);
        }

        return $consulta->exists();
    }

    public function index(Request $request)
    {
        $categorias = Categoria::orderBy('nombre')->get();

        $query = Producto::with('categoria')
            ->withCount('calificaciones')
            ->withAvg('calificaciones', 'puntuacion');

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
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
        // Normalizar espacios antes de validar y guardar.
        $request->merge([
            'nombre' => trim((string) $request->input('nombre', '')),
        ]);

        $datosValidados = $request->validate(
            [
                'categoria_id' => 'required|exists:categorias,id',
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'precio' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            ],
            [
                'nombre.required' => 'El nombre del producto es obligatorio.',
                'nombre.max' => 'El nombre del producto no puede superar los 255 caracteres.',
            ]
        );

        // Los nombres de productos deben ser únicos en todo el catálogo,
        // incluso cuando pertenecen a categorías diferentes.
        if ($this->nombreYaExiste($datosValidados['nombre'])) {
            return back()
                ->withErrors([
                    'nombre' => 'Ya existe un producto con ese nombre. Elige un nombre diferente.',
                ])
                ->withInput();
        }

        $datos = $request->only([
            'categoria_id',
            'nombre',
            'descripcion',
            'precio',
            'stock',
        ]);

        $datos['estado'] = 'disponible';

        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')->store(
                'productos',
                'public'
            );
        }

        Producto::create($datos);

        return redirect()
            ->route('admin.productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function edit($id)
    {
        $producto = Producto::findOrFail($id);
        $categorias = Categoria::all();

        return view(
            'admin.productos.edit',
            compact('producto', 'categorias')
        );
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $request->merge([
            'nombre' => trim((string) $request->input('nombre', '')),
        ]);

        $datosValidados = $request->validate(
            [
                'categoria_id' => 'required|exists:categorias,id',
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'precio' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'estado' => 'required|in:disponible,agotado',
                'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            ],
            [
                'nombre.required' => 'El nombre del producto es obligatorio.',
                'nombre.max' => 'El nombre del producto no puede superar los 255 caracteres.',
            ]
        );

        // Al editar se excluye el producto actual para permitir conservar
        // su propio nombre, pero no utilizar el de otro producto existente.
        if ($this->nombreYaExiste($datosValidados['nombre'], (int) $producto->id)) {
            return back()
                ->withErrors([
                    'nombre' => 'Ya existe otro producto con ese nombre. Elige un nombre diferente.',
                ])
                ->withInput();
        }

        $datos = $request->only([
            'categoria_id',
            'nombre',
            'descripcion',
            'precio',
            'stock',
            'estado',
        ]);

        // Guardar y sustituir la imagen solo cuando se selecciona una nueva.
        if ($request->hasFile('imagen')) {
            $imagenAnterior = $producto->imagen;

            $nuevaImagen = $request->file('imagen')
                ->store('productos', 'public');

            $datos['imagen'] = $nuevaImagen;

            $producto->update($datos);

            if (
                $imagenAnterior &&
                Storage::disk('public')->exists($imagenAnterior)
            ) {
                Storage::disk('public')->delete($imagenAnterior);
            }
        } else {
            $producto->update($datos);
        }

        return redirect()
            ->route('admin.productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Mostrar las calificaciones de un producto.
     */
    public function calificaciones(
        Request $request,
        int $id,
        FechaFiltroService $fechas
    ) {
        $fechaSeleccionada = $fechas->resolver($request);

        [$inicioUtc, $finUtc] = $fechas->rangoUtc(
            $fechaSeleccionada
        );

        $producto = Producto::with('categoria')
            ->findOrFail($id);

        $calificaciones = Calificacion::where(
            'producto_id',
            $producto->id
        )
            ->whereBetween('created_at', [$inicioUtc, $finUtc])
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
                'cantidadEstrellas',
                'fechaSeleccionada'
            )
        );
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);

        if ($producto->imagen) {
            Storage::disk('public')->delete($producto->imagen);
        }

        $producto->delete();

        return back()->with(
            'success',
            'Producto eliminado correctamente'
        );
    }
}
