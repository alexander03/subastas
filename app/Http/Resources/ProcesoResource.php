<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\MediaResource;

class ProcesoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return ['id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'fechainicio' => $this->fechainicio,
            'fechafin' => $this->fechafin,
            'tipo' => $this->tipo,
            'situacion' => $this->situacion,
            'modalidad' => $this->modalidad,
            'bases' => $this->bases,
            'preciobase' => $this->preciobase,
            'proceso_id' => $this->proceso_id,
            'proceso' =>new ProcesoResource($this->proceso),
            'tiempo' => $this->tiempo,
            'media' => MediaResource::collection($this->media),
            'empresa_id' => $this->empresa_id,
            'empresa' =>new EmpresaResource($this->empresa),
        ];
    }
}
