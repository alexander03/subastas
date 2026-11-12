<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ContactoResource;
use App\Models\Contacto;

class ContactoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $lista = ContactoResource::collection((Contacto::latest()->paginate()));
        return response()->json($lista);
    }

    public function list()
    {
        $lista = Contacto::get();
        return response()->json($lista);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombres' => 'required',
            'apellidos' => 'required',
            'tipodocumento' => 'required',
            'numero' => 'required',
            'correo' => 'required',
            'telefono' => 'required',
            'mensaje' => 'required',
        ]);
        $obj = Contacto::create($request->all());
        return response()->json($obj);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Categoria  $categoria
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $obj = Contacto::find($id);
        return new ContactoResource($obj);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Categoria  $categoria
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request)
    {
        $obj = Contacto::findOrFail($id);
        
        $data = $request->validate([
            'nombres' => 'required',
            'apellidos' => 'required',
            'tipodocumento' => 'required',
            'numero' => 'required',
            'correo' => 'required',
            'telefono' => 'required',
            'mensaje' => 'required',
        ]);

        $obj->update($request->all());
        return response()->json($obj);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Categoria  $categoria
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $obj = Contacto::find($id);
        if(!empty($obj)){
            $obj->delete();
            return response()->json($obj);
        }

        return response()->json('No encontrado');
    }
}
