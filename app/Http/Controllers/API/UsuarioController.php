<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepartamentoResource;
use App\Http\Resources\DistritoResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UsuarioResource;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lista = UsuarioResource::collection((User::latest()->paginate()));
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
        $obj = User::create($request->all());
        $obj = new UsuarioResource($obj);
        return response()->json($obj);
    }

    public function store2(Request $request)
    {
        $obj = User::create($request->all());
        $obj = new UsuarioResource($obj);
        return response()->json($obj);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $obj = User::find($id);
        return new UsuarioResource($obj);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request)
    {
        $obj = User::find($id);
        $obj->update($request->all());
        $obj = new UsuarioResource($obj);
        return response()->json($obj);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $obj = User::find($id);
        if(!empty($obj)){
            $obj->delete();
            return response()->json($obj);
        }

        return response()->json('No encontrado');
    }
}
