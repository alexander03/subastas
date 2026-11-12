<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Models\Bien;
use Illuminate\Http\Request;
use App\Http\Resources\SliderResource;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lista = SliderResource::collection((Slider::latest()->paginate()));
        return response()->json($lista);
    }

    public function list()
    {
        $lista = Slider::join('biens','biens.producto_id','=','sliders.producto_id')
                ->join('procesos','procesos.id','=','biens.proceso_id')
                ->join('etapas','etapas.proceso_id','=','procesos.id')
                ->join('productos','productos.id','=','biens.producto_id')
                //->where('biens.situacion','like','P')
                ->whereNull('productos.deleted_at')
                ->whereNull('biens.deleted_at')
                ->whereNull('procesos.deleted_at')
                ->where('etapas.tipo','like','3')
                ->orderBy('etapas.fechainicio','asc')
                ->limit(10)
                ->select('sliders.*','sliders.id as sliders_id','biens.id as bien_id','biens.situacion','biens.valorreferencia','biens.producto_id',
                'biens.proceso_id','biens.garantia','etapas.fechainicio','etapas.fechafin')
                ->get();
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
            'producto_id' => 'required|exists:productos,id',
        ]);

        $obj = new Slider();
        $obj->producto_id = $data['producto_id'];

        if (isset($request['image'])) {
            $obj->clearMediaCollection('images');
            $obj->addMediaFromRequest('image')->toMediaCollection('images');
        }
        $obj->save();
        $obj->load(['producto','media']);
        return response()->json($obj);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Producto  $producto
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $obj = Slider::find($id);
        $obj->load(['producto']);
        return new SliderResource($obj);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Producto  $producto
     * @return \Illuminate\Http\Response
     */
    public function actualizar($id,Request $request)
    {
        $obj = Slider::findOrFail($id);

        $data = $request->validate([
            'producto_id' => 'required|exists:productos,id',
        ]);
        $obj->producto_id = $data['producto_id'];

        if (isset($request['image'])) {
            $obj->clearMediaCollection('images');
            $obj->addMediaFromRequest('image')->toMediaCollection('images');
        }
        $obj->save();
        $obj->load(['producto','media']);
        return response()->json($obj);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Producto  $producto
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $obj = Slider::find($id);
        if(!empty($obj)){
            $obj->delete();
            return response()->json($obj);
        }

        return response()->json('No encontrado');
    }
}
