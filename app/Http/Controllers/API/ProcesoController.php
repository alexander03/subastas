<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Proceso;
use App\Models\Bien;
use App\Models\Producto;
use App\Models\Etapa;
use App\Http\Resources\ProcesoResource;
use App\Models\Bases;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ProcesoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lista = ProcesoResource::collection((Proceso::latest()->paginate()));
        return response()->json($lista);
    }

    public function list()
    {
        $lista = Proceso::with(['bienes', 'etapas','media','empresa'])->latest()->get();
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
        $obj = Proceso::create($request->all());
        return response()->json($obj);
    }

    public function store2(Request $request)
    {
        $data = json_decode($request->input('data'));
        $bienes = $data->products;
        $etapas = $data->stages;
        $obj = Proceso::create((array) $data->process);
        foreach ($bienes as $key => $value) {
            $producto = Producto::find(intval($value->id));
            if (!is_null($producto)) {
                $input = array(
                    "producto_id" => intval($value->id),
                    "proceso_id" => $obj->id,
                    "situacion" => 'P',
                    "garantia" => $value->garantia,
                    "valorreferencia" => $producto->valor
                );
                Bien::create($input);
            }
        }
        foreach ($etapas as $key => $value) {
            $input = $value;
            $input->proceso_id = $obj->id;
            Etapa::create((array) $input);
        }
        if (isset($request['image'])) {
            $obj->clearMediaCollection('images');
            $obj->addMediaFromRequest('image')->toMediaCollection('images');
        }
        if (isset($request['bases'])) {
            $obj->clearMediaCollection('bases');
            $obj->addMediaFromRequest('bases')->toMediaCollection('bases');
        }
        $obj->load(['bienes', 'etapas','empresa']);
        return response()->json($obj);
    }

    public function show($id)
    {
        $obj = Proceso::find($id);
        return new ProcesoResource($obj);
    }

    public function update2(Request $request)
    {
        $id = $request->input('id');
        $obj = Proceso::find($id);
        $data = json_decode($request->input('data'));
        $bienes = $data->products;
        $etapas = $data->stages;
        $obj->update((array) $data->process);

        foreach ($bienes as $key => $value) {
            $producto = Producto::find(intval($value->id));
            if (!is_null($producto)) {
                $bien = Bien::where('proceso_id',$id)->where('producto_id','=',intval($value->id))->first();
                if(is_null($bien)){
                    $input = array(
                        "producto_id" => intval($value->id),
                        "proceso_id" => $obj->id,
                        "situacion" => 'P',
                        "garantia" => $value->garantia,
                        "valorreferencia" => $producto->valor
                    );
                    Bien::create($input);
                }else{
                    $input = array(
                        "producto_id" => intval($value->id),
                        "proceso_id" => $obj->id,
                        "garantia" => $value->garantia,
                        "valorreferencia" => $producto->valor
                    );
                    $bien->update((array)$input);
                }
            }
        }
        $listaBienes = Bien::where('proceso_id',$id)->get();
        foreach($listaBienes as $k=>$v){
            $band=false;
            foreach ($bienes as $key => $value) {
                if(intval($value->id)==$v->producto_id){
                    $band=true;
                }
            }
            if(!$band){
                $v->delete();
            }
        }
        foreach ($etapas as $key => $value) {
            $input = $value;
            $etapa = Etapa::where('proceso_id','=',$obj->id)->where('tipo','like',$value->tipo)->first();
            $input->proceso_id = $obj->id;
            $etapa->update((array) $input);
        }

        if (isset($request['image'])) {
            $obj->clearMediaCollection('images');
            $obj->addMediaFromRequest('image')->toMediaCollection('images');
        }
        if (isset($request['bases'])) {
            $obj->clearMediaCollection('bases');
            $obj->addMediaFromRequest('bases')->toMediaCollection('bases');
        }
        $obj->load(['bienes', 'etapas','empresa']);
        return response()->json($obj);
    }

    public function destroy($id)
    {
        $obj = Proceso::find($id);
        if (!empty($obj)) {
            $obj->delete();
            return response()->json($obj);
        }

        return response()->json('No encontrado');
    }

    public function obtenerProcesosDeUsuario($usuarioId)
    {
        $usuario = User::findOrFail($usuarioId);

        // Accede a las procesos del usuario
        $procesos = $usuario->procesos;

        // $procesos contendrá una colección de objetos Inscripcion
        return response()->json($procesos);
    }

    public function obtenerGanadorProceso(Request $request)
    {
        $request->validate([
            'proceso_id' => 'nullable|integer',
            'bien_id' => 'nullable|integer',
        ]);
        $proceso_id = $request->input('proceso_id');
        $bien_id = $request->input('bien_id');
        $lista = Bien::where('situacion','like','F');
        if($proceso_id!="0"){
            $lista = $lista->where('proceso_id','=',$proceso_id);
        }
        if($bien_id!="0"){
            $lista = $lista->where('id','=',$bien_id);
        }
        // Informacion con las puja ganadora
        $data = $lista->with('bid')->get();
        // $procesos contendrá una colección de objetos Inscripcion
        return response()->json($data);
    }

    public function obtenerProcesosUltimos(){
        $data = Proceso::join('etapas','etapas.proceso_id','=','procesos.id')
                    ->where('etapas.tipo','like','3')
                    ->orderBy('etapas.fechainicio','asc')
                    ->with(['media','bienes','empresa','etapas'])
                    ->select('procesos.*')
                    ->get();
        return response()->json($data);
    }

    public function obtenerFiltroWeb(Request $request){
        $request->validate([
            'modalidad' => 'nullable|string',
            'categoria_id' => 'nullable|integer',
            'provincia' => 'nullable|string',
            'empresa_id' => 'nullable|integer',
            'preciomin' => 'nullable|numeric',
            'preciomax' => 'nullable|numeric',
        ]);
        $lista = Proceso::join('biens','biens.proceso_id','=','procesos.id')
                ->join('etapas','etapas.proceso_id','=','procesos.id')
                ->join('productos','productos.id','=','biens.producto_id')
                //->where('biens.situacion','like','P')
                ->where('etapas.tipo','like','3')
                ->orderBy('etapas.fechainicio','asc')
                ->whereNull('productos.deleted_at');
        if($request->input('modalidad')!="" && !is_null($request->input('modalidad'))){
            $lista = $lista->where('procesos.modalidad','like',$request->input('modalidad'));
        }
        if($request->input('categoria_id')!="" && !is_null($request->input('categoria_id'))){
            $lista = $lista->where('productos.categoria_id','=',$request->input('categoria_id'));
        }
        if($request->input('provincia')!="" && !is_null($request->input('provincia'))){
            $lista = $lista->where('productos.distrito_id','like',$request->input('provincia').'%');
        }
        if($request->input('empresa_id')!="" && !is_null($request->input('empresa_id'))){
            $lista = $lista->where('procesos.empresa_id','=',$request->input('empresa_id'));
        }
        if($request->input('preciomin')!="" && !is_null($request->input('preciomin'))){
            $lista = $lista->where('biens.valorreferencia','>=',$request->input('preciomin'));
        }
        if($request->input('preciomax')!="" && !is_null($request->input('preciomax'))){
            $lista = $lista->where('biens.valorreferencia','<=',$request->input('preciomax'));
        }
        $lista = $lista->select('procesos.*')
                    ->with(['media','bienes','empresa','etapas'])
                    ->get();
        return response()->json($lista);
    }

    public function obtenerProceso(Request $request){
        // Solo documenta los parametros en Scramble; son opcionales para no cambiar el comportamiento actual.
        $request->validate([
            'proceso_id' => 'nullable|integer',
            'bien_id' => 'nullable|integer',
        ]);
        $lista = Proceso::join('biens','biens.proceso_id','=','procesos.id')
                ->join('productos','productos.id','=','biens.producto_id')
                //->where('biens.situacion','like','P')
                ->whereNull('productos.deleted_at')
                ->where('procesos.id','=',$request->input('proceso_id'))
                ->where('biens.id','=',$request->input('bien_id'));
        $lista = $lista->select('procesos.*')
                    ->orderBy('procesos.fechainicio','asc')
                    ->with('bienes', function ($query) use($request) {
                        $query->where('id','=',$request->input('bien_id'));
                    })
                    ->with(['media'])
                    ->get();
        return response()->json($lista);
    }

    public function listaPDF(Request $request)
    {
        $request->validate([
            'fechainicio' => 'nullable|date',
            'fechafin' => 'nullable|date',
        ]);
        $lista = Proceso::where('procesos.fechainicio','>=',$request->input('fechainicio'));
        if(!is_null($request->input('fechafin')) && !empty($request->input('fechafin'))){
            $lista = $lista->where('procesos.fechainicio','<=',$request->input('fechafin'));
        }
        $lista = $lista->select('procesos.*')
                    ->orderBy('procesos.fechainicio','asc')
                    ->with(['media','empresa'])
                    ->get();
        
        $pdf = Pdf::loadView('proceso', [
            'lista' => $lista,
        ]);

        //return $pdf->stream('proceso.pdf');
        return $pdf->download('procesos_' . date('Y-m-d') . '.pdf');
    }

    public function listaBienesPDF(Request $request)
    {
        $request->validate([
            'proceso_id' => 'nullable|integer',
        ]);
        $lista = Proceso::join('biens','biens.proceso_id','=','procesos.id')
                ->join('productos','productos.id','=','biens.producto_id')
                ->whereNull('productos.deleted_at');
        if(!is_null($request->input('proceso_id')) && $request->input('proceso_id')!=""){
            $lista = $lista->where('procesos.id','=',$request->input('proceso_id'));
        }
        $lista = $lista->select('procesos.*','biens.id as bien_id')
                    ->orderBy('procesos.fechainicio','asc')
                    ->with(['media','empresa','bienes'])
                    ->get();
        
        $pdf = Pdf::loadView('procesoBien', [
            'lista' => $lista,
        ]);

        //return $pdf->stream('proceso.pdf');
        return $pdf->download('procesosBienes_' . date('Y-m-d') . '.pdf');
    }

    public function listaParticipantesPDF(Request $request)
    {
        $request->validate([
            'proceso_id' => 'nullable|integer',
            'bien_id' => 'nullable|integer',
        ]);
        $proceso_id = $request->input('proceso_id');
        $bien_id = $request->input('bien_id');
        $lista = Proceso::join('biens','biens.proceso_id','=','procesos.id')
                ->join('productos','productos.id','=','biens.producto_id')
                ->join('inscripcions','inscripcions.bien_id','=','biens.id')
                ->join('users','inscripcions.usuario_id','=','users.id')
                ->whereNull('productos.deleted_at');
        if(!is_null($proceso_id) && $proceso_id!=""){
            $lista = $lista->where('procesos.id','=',$proceso_id);
        }
        if(!is_null($bien_id) && $bien_id!=""){
            $lista = $lista->where('biens.id','=',$bien_id);
        }
        $lista = $lista->select('procesos.*','biens.id as bien_id','users.nombres','users.apellidopaterno','users.apellidomaterno','inscripcions.situacion as situacion_inscripcion')
                    ->orderBy('procesos.fechainicio','asc')
                    ->with(['media','empresa','bienes'])
                    ->get();
        
        $pdf = Pdf::loadView('procesoParticipantes', [
            'lista' => $lista,
        ]);

        //return $pdf->stream('proceso.pdf');
        return $pdf->download('procesosParticipantes_' . date('Y-m-d') . '.pdf');
        
    }

    public function listaGanadorPDF(Request $request)
    {
        $request->validate([
            'proceso_id' => 'nullable|integer',
            'bien_id' => 'nullable|integer',
        ]);
        $proceso_id = $request->input('proceso_id');
        $bien_id = $request->input('bien_id');
        $lista = Proceso::join('biens','biens.proceso_id','=','procesos.id')
                ->join('productos','productos.id','=','biens.producto_id')
                ->join('inscripcions','inscripcions.bien_id','=','biens.id')
                ->join('users','inscripcions.usuario_id','=','users.id')
                ->where('biens.situacion','like','F')
                ->whereNull('productos.deleted_at');
        if(!is_null($proceso_id) && $proceso_id!=""){
            $lista = $lista->where('procesos.id','=',$proceso_id);
        }
        if(!is_null($bien_id) && $bien_id!=""){
            $lista = $lista->where('biens.id','=',$bien_id);
        }
        $lista = $lista->select('procesos.*','biens.id as bien_id','users.nombres','users.apellidopaterno','users.apellidomaterno','inscripcions.situacion as situacion_inscripcion')
                    ->orderBy('procesos.fechainicio','asc')
                    ->with(['media','empresa','bienes'])
                    ->get();
        
        $pdf = Pdf::loadView('procesoGanadores', [
            'lista' => $lista,
        ]);

        //return $pdf->stream('proceso.pdf');
        return $pdf->download('procesosGanador_' . date('Y-m-d') . '.pdf');
        
    }

}
