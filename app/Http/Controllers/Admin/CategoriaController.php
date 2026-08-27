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

            'nombre'=>'required',

            'descripcion'=>'nullable'

        ]);




        Categoria::create([

            'nombre'=>$request->nombre,

            'descripcion'=>$request->descripcion,

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

            'nombre'=>'required',

            'descripcion'=>'nullable'

        ]);




        $categoria->update([

            'nombre'=>$request->nombre,

            'descripcion'=>$request->descripcion

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


        $categoria->delete();



        return back()->with(

            'success',

            'Categoría eliminada correctamente'

        );


    }


}