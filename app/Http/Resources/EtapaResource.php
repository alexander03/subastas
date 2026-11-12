<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EtapaResource extends JsonResource
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
            'tipo' => $this->tipo,
            'fechainicio' => $this->fechainicio,
            'fechafin' => $this->fechafin,
            'situacion' => $this->situacion,
            'proceso_id' => $this->proceso_id,
            'proceso' => new ProcesoResource($this->proceso),
            'etapa_id' => $this->etapa_id,
            'etapa' => new EtapaResource($this->etapa),
        ];
    }
}
