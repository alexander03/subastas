<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmpresaResource;
use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $lista = EmpresaResource::collection((Empresa::latest()->paginate()));
        return response()->json($lista);
    }

    public function list()
    {
        $lista = Empresa::get();
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
            'ruc' => 'required|unique:empresas,ruc,NULL,id,deleted_at,NULL',
            'razonsocial' => 'required',
        ]);
        $obj = Empresa::create($request->all());
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
        $obj = Empresa::find($id);
        return new EmpresaResource($obj);
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
        $obj = Empresa::findOrFail($id);
        // return response()->json($request);
        // dd();

        $data = $request->validate([
            'ruc' => 'required|unique:empresas,ruc,'.$id.',id,deleted_at,NULL',
            'razonsocial' => 'required',
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
        $obj = Empresa::find($id);
        if(!empty($obj)){
            $obj->delete();
            return response()->json($obj);
        }

        return response()->json('No encontrado');
    }
}
