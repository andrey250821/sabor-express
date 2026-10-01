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


        return view(
            'admin.categorias.create'
        );


    }





    public function store(Request $request)
    {


        $request->validate([

            'nombre'=>'required'

        ]);




        Categoria::create([

            'nombre'=>$request->nombre,

            'estado'=>'activo'

        ]);




        return redirect()

            ->route('admin.categorias.index')

            ->with(
                'success',
                'Categoría creada correctamente'
            );


    }







    public function edit($id)
    {


        $categoria = Categoria::findOrFail($id);



        return view(

            'admin.categorias.edit',

            compact('categoria')

        );


    }







    public function update(Request $request,$id)
    {


        $categoria = Categoria::findOrFail($id);



        $request->validate([

            'nombre'=>'required'

        ]);




        $categoria->update([

            'nombre'=>$request->nombre

        ]);




        return redirect()

            ->route('admin.categorias.index')

            ->with(
                'success',
                'Categoría actualizada correctamente'
            );


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