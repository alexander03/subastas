<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Http\Resources\ProductoResource;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lista = ProductoResource::collection((Producto::latest()->paginate()));
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
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required',
            'descripcion' => 'required',
            'valor' => 'required',
            'situacion' => 'required',
            'moneda' => 'required',
            'distrito_id' => 'required|exists:ubigeo_peru_districts,id',
        ]);

        $obj = new Producto();
        $obj->categoria_id = $data['categoria_id'];
        $obj->nombre = $data['nombre'];
        $obj->descripcion = $data['descripcion'];
        $obj->portada = 'null';
        $obj->valor = $data['valor'];
        $obj->situacion = $data['situacion'];
        $obj->distrito_id = $data['distrito_id'];
        $obj->moneda = $data['moneda'];

        if (isset($request['image'])) {
            $obj->clearMediaCollection('images');
            foreach ($request->file('image') as $photo) {
                $obj->addMedia($photo)->toMediaCollection('images');
            }
            //$obj->addMediaFromRequest('image')->toMediaCollection('images');
        }
        $obj->save();
        $obj->load(['categoria','media','distrito']);
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
        $obj = Producto::find($id);
        $obj->load(['categoria','distrito']);
        return new ProductoResource($obj);
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
        // $obj = Producto::find($id);
        $obj = Producto::findOrFail($id);
        // return response()->json($request);
        // dd();

        $data = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required',
            'descripcion' => 'required',
            'valor' => 'required',
            'situacion' => 'required',
            'moneda' => 'required',
            'distrito_id' => 'required|exists:ubigeo_peru_districts,id',
        ]);
        $obj->categoria_id = $data['categoria_id'];
        $obj->nombre = $data['nombre'];
        $obj->descripcion = $data['descripcion'];
        $obj->portada = 'null';
        $obj->valor = $data['valor'];
        $obj->situacion = $data['situacion'];
        $obj->distrito_id = $data['distrito_id'];
        $obj->moneda = $data['moneda'];

        if (isset($request['image'])) {
            $obj->clearMediaCollection('images');
            foreach ($request->file('image') as $photo) {
                $obj->addMedia($photo)->toMediaCollection('images');
            }
            //$obj->addMediaFromRequest('image')->toMediaCollection('images');
        }
        $obj->save();
        $obj->load(['categoria','media','distrito']);
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
        $obj = Producto::find($id);
        if(!empty($obj)){
            $obj->delete();
            return response()->json($obj);
        }

        return response()->json('No encontrado');
    }
}
