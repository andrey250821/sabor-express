<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::all();

        return view(
            'admin.categorias.index',
            compact('categorias')
        );
    }

    public function create()
    {
        return view('admin.categorias.create');
    }

    /**
     * Comprobar nombres duplicados ignorando mayúsculas y espacios
     * al inicio o al final. No modifica la base de datos.
     */
    private function nombreYaExiste(string $nombre, ?int $excluirId = null): bool
    {
        $consulta = Categoria::query()
            ->whereRaw('LOWER(TRIM(nombre)) = ?', [
                mb_strtolower(trim($nombre), 'UTF-8'),
            ]);

        if ($excluirId !== null) {
            $consulta->where('id', '!=', $excluirId);
        }

        return $consulta->exists();
    }

    public function store(Request $request)
    {
        $request->merge([
            'nombre' => trim((string) $request->input('nombre', '')),
        ]);

        $datos = $request->validate(
            [
                'nombre' => ['required', 'string', 'max:255'],
            ],
            [
                'nombre.required' => 'El nombre de la categoría es obligatorio.',
                'nombre.max' => 'El nombre de la categoría no puede superar los 255 caracteres.',
            ]
        );

        if ($this->nombreYaExiste($datos['nombre'])) {
            return back()
                ->withErrors([
                    'nombre' => 'Ya existe una categoría con ese nombre. Elige un nombre diferente.',
                ])
                ->withInput();
        }

        Categoria::create([
            'nombre' => $datos['nombre'],
            'estado' => 'activo',
        ]);

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', 'Categoría creada correctamente');
    }

    public function edit($id)
    {
        $categoria = Categoria::findOrFail($id);

        return view(
            'admin.categorias.edit',
            compact('categoria')
        );
    }

    public function update(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);

        $request->merge([
            'nombre' => trim((string) $request->input('nombre', '')),
        ]);

        $datos = $request->validate(
            [
                'nombre' => ['required', 'string', 'max:255'],
            ],
            [
                'nombre.required' => 'El nombre de la categoría es obligatorio.',
                'nombre.max' => 'El nombre de la categoría no puede superar los 255 caracteres.',
            ]
        );

        if ($this->nombreYaExiste($datos['nombre'], (int) $categoria->id)) {
            return back()
                ->withErrors([
                    'nombre' => 'Ya existe otra categoría con ese nombre. Elige un nombre diferente.',
                ])
                ->withInput();
        }

        $categoria->update([
            'nombre' => $datos['nombre'],
        ]);

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', 'Categoría actualizada correctamente');
    }

    public function destroy($id)
    {
        $categoria = Categoria::findOrFail($id);

        if ($categoria->productos()->exists()) {
            return back()->with(
                'error',
                'No se puede eliminar esta categoría porque tiene productos asociados. Puedes inactivarla para conservar su información.'
            );
        }

        $categoria->delete();

        return back()->with(
            'success',
            'Categoría eliminada correctamente'
        );
    }

    /**
     * Inactivar categoría sin eliminarla.
     */
    public function inactivar($id)
    {
        $categoria = Categoria::findOrFail($id);

        $categoria->estado = 'inactivo';
        $categoria->save();

        return back()->with(
            'success',
            'Categoría inactivada correctamente.'
        );
    }

    /**
     * Activar nuevamente una categoría.
     */
    public function activar($id)
    {
        $categoria = Categoria::findOrFail($id);

        $categoria->estado = 'activo';
        $categoria->save();

        return back()->with(
            'success',
            'Categoría activada correctamente.'
        );
    }
}
