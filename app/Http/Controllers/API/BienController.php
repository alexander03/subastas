<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bien;
use App\Http\Resources\BienResource;
use Illuminate\Http\Request;
use App\Providers\Subasta;
use App\Providers\Contador;
use Illuminate\Support\Facades\Queue;

class BienController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $lista = BienResource::collection((Bien::where('proceso_id', '=', $request->input('proceso_id'))->latest()->paginate()));
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
        $obj = Bien::create($request->all());
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
        $obj = Bien::with('proceso')->findOrFail($id);
        $obj->descripcionProceso = $obj->proceso->descripcion;
        unset($obj->proceso);
        return response()->json($obj);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $obj = Bien::find($id);
        $obj->update($request->all());
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
        $obj = Bien::find($id);
        if (!empty($obj)) {
            $obj->delete();
            return response()->json($obj);
        }

        return response()->json('No encontrado');
    }

    public function empezarSubasta(Request $request){
        $bien_id = $request->input('bien_id');
        $bien = Bien::find($bien_id);
        if(is_null($bien->proceso->tiempo)){
            return response()->json("Tiempo del proceso es nulo");
        }
        if($bien->situacion=='P'){
            $bien->situacion='E';
            $bien->fechainicio = date("Y-m-d H:i:s");
            $bien->segundoactual = $bien->proceso->tiempo;
            $bien->save();
            $mensaje = ["contador"=>"-1","mensaje"=>"Empezo subasta"];
            Contador::dispatch($bien,$mensaje);
   
            return response()->json("Empezo subasta");
        }elseif($bien->situacion=='E'){
            return response()->json("Subasta en proceso");
        }elseif($bien->situacion=='F'){
            return response()->json("Subasta ya finalizada");
        }
    }

    public function empezarSubastaManual(Request $request){
        $bien_id = $request->input('bien_id');
        $bien = Bien::find($bien_id);
        if($bien->situacion=='P'){
            $bien->situacion='E';
            $bien->fechainicio = date("Y-m-d H:i:s");
            $bien->segundoactual = null;
            $bien->save();
            $mensaje = ["contador"=>"-1","mensaje"=>"Empezo subasta"];
            Subasta::dispatch($bien,$mensaje);
            return response()->json("Empezo subasta");
        }elseif($bien->situacion=='E'){
            return response()->json("Subasta en proceso");
        }elseif($bien->situacion=='F'){
            return response()->json("Subasta ya finalizada");
        }
    }

    public function contadorSubastaManual(Request $request){
        $bien_id = $request->input('bien_id');
        $bien = Bien::find($bien_id);
        if($bien->situacion=='E'){
            $bien->segundoactual = $bien->proceso->tiempo;
            $bien->save();
            $mensaje = ["contador"=>$bien->segundoactual,"mensaje"=>"Conteo subasta"];
            Subasta::dispatch($bien,$mensaje);
            return response()->json("Empezo contador");
        }elseif($bien->situacion=='P'){
            return response()->json("Subasta no iniciada");
        }elseif($bien->situacion=='F'){
            return response()->json("Subasta ya finalizada");
        }
    }

    public function finalizarSubastaManual(Request $request){
        $bien_id = $request->input('bien_id');
        $bien = Bien::find($bien_id);
        if($bien->situacion=='E'){
            $bien->situacion='F';
            $bien->fechafin = date("Y-m-d H:i:s");
            $bien->save();
            $mensaje = ["contador"=>"-3","mensaje"=>"Finalizo subasta"];
            Subasta::dispatch($bien,$mensaje);
            return response()->json("Finalizo subasta");
        }elseif($bien->situacion=='P'){
            return response()->json("Subasta no iniciada");
        }elseif($bien->situacion=='F'){
            return response()->json("Subasta ya finalizada");
        }
    }
}
