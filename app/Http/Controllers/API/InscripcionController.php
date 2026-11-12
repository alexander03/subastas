<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inscripcion;
use App\Http\Resources\InscripcionResource;
use App\Models\Bases;
use App\Models\Bien;
use Illuminate\Support\Facades\DB;

class InscripcionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $list = Inscripcion::orderBy('fecha','asc');
        if(!is_null($request->input('bien_id')) && !empty($request->input('bien_id'))){
            $list = $list->where('bien_id','=',$request->input('bien_id'));
        }
        if(!is_null($request->input('etapa_id')) && !empty($request->input('etapa_id'))){
            $list = $list->where('etapa_id','=',$request->input('etapa_id'));
        }

        $list = $list->latest()->paginate();
        $lista = InscripcionResource::collection($list);
        return response()->json($lista);
    }

    public function inscripcionesByUser(Request $request)
    {
        $list = Inscripcion::orderBy('fecha','asc');
        if(!is_null($request->input('usuario_id')) && !empty($request->input('usuario_id'))){
            $list = $list->where('usuario_id','=',$request->input('usuario_id'));
        }

        $list = $list->get();
        $lista = InscripcionResource::collection($list);
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
        $obj = "";
        $error = DB::transaction(function() use($request,&$obj){
            $obj = Inscripcion::create($request->all());
            if (isset($request['image'])) {
                $obj->clearMediaCollection('images');
                $obj->addMediaFromRequest('image')->toMediaCollection('images');
                $obj->save();
            }
            $bien = Bien::find($request->bien_id);
            if($bien->proceso->bases=="S"){
                $base = array("proceso_id"=>$bien->proceso_id,
                            "situacion"=>'P',
                            "comentario"=>'',
                            "usuario_id"=>$request->usuario_id);
                Bases::create($base);
            }

            $obj->load(['media']);
        });
        
        return response()->json($obj);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $obj = Inscripcion::find($id);
        return new InscripcionResource($obj);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function actualizar($id,Request $request)
    {
        $obj = Inscripcion::find($id);
        $obj->update($request->all());

        if (isset($request['image'])) {
            $obj->clearMediaCollection('images');
            $obj->addMediaFromRequest('image')->toMediaCollection('images');
            $obj->save();
        }

        $obj->load(['media']);
        $obj = new InscripcionResource($obj);


        return response()->json($obj);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $obj = Inscripcion::find($id);
        if(!empty($obj)){
            $obj->delete();
            return response()->json($obj);
        }

        return response()->json('No encontrado');
    }
}
