<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Departamento;
use App\Models\Provincia;
use App\Models\Distrito;
use App\Http\Resources\DistritoResource;
use App\Http\Resources\ProvinciaResource;
use App\Http\Resources\DepartamentoResource;

class UbigeoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function distrito(Request $request)
    {
        $obj = Distrito::where('province_id','=',$request->input('provincia_id'))->orderBy('name')->get();
        return DistritoResource::collection($obj);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function provincia(Request $request)
    {
        $obj = Provincia::where('department_id','=',$request->input('departamento_id'))->orderBy('name')->get();
        return ProvinciaResource::collection($obj);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function departamento(Request $request)
    {
        $obj = Departamento::orderBy('name')->get();
        return DepartamentoResource::collection($obj);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
