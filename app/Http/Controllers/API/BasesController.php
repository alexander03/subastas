<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bases;
use Illuminate\Http\Request;
use App\Http\Resources\BasesResource;
use Faker\Provider\Base;

class BasesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lista = BasesResource::collection((Bases::latest()->paginate()));
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
        $obj = Bases::create($request->all());
        return response()->json($obj);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bases  $bases
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $obj = Bases::find($id);
        return new BasesResource($obj);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bases  $bases
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $obj = Bases::find($id);
        $obj->update($request->all());
        return response()->json($obj);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bases  $bases
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $obj = Bases::find($id);
        if(!empty($obj)){
            $obj->delete();
            return response()->json($obj);
        }

        return response()->json('No encontrado');
    }
}
