<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CategoriaResource;
use App\Http\Resources\SliderResource;
use App\Http\Resources\MediaResource;
use App\Http\Resources\DistritoResource;

class ProductoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return ['id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'valor' => $this->valor,
            'portada' => $this->portada,
            'situacion' => $this->situacion,
            'categoria_id' => $this->categoria_id,
            'categoria' =>new CategoriaResource($this->categoria),
            'media' => MediaResource::collection($this->media),
            'distrito' => new DistritoResource($this->distrito),
            'moneda' => $this->moneda,
            //'slider' => new SliderResource($this->slider),
        ];
    }
}
