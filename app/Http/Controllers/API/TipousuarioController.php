<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tipousuario;
use App\Models\Acceso;
use Illuminate\Http\Request;
use App\Http\Resources\TipousuarioResource;

class TipousuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lista = TipousuarioResource::collection((Tipousuario::latest()->paginate()));
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
        $obj = Tipousuario::create($request->all());
        return response()->json($obj);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tipousuario  $tipousuario
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $obj = Tipousuario::find($id);
        return new TipousuarioResource($obj);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tipousuario  $tipousuario
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request)
    {
        $obj = Tipousuario::find($id);
        $obj->update($request->all());
        return response()->json($obj);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tipousuario  $tipousuario
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $obj = Tipousuario::find($id);
        if(!empty($obj)){
            $obj->delete();
            return response()->json($obj);
        }

        return response()->json('No encontrado');
    }

    public function acceso(Request $request)
    {
        $obj = Tipousuario::find($request->input('id'));
        $lista = Acceso::where('tipousuario_id','=',$obj->id)->get();
        if(count($lista)>0){
            foreach($lista as $k){
                $k->delete();
            }
        }
        $accesos = json_decode($request->input('accesos'));
        foreach($accesos as $k){
            $acceso = new Acceso();
            $acceso->tipousuario_id = $obj->id;
            $acceso->opcionmenu_id = $k;
            $acceso->save();
        }
        return response()->json($obj);
    }
}
