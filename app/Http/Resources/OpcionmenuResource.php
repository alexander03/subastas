<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\GrupomenuResource;

class OpcionmenuResource extends JsonResource
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
            'ruta' => $this->ruta,
            'orden' => $this->orden,
            'icono' => $this->icono,
            'grupomenu_id' => $this->grupomenu_id,
            'grupomenu' =>new GrupomenuResource($this->grupomenu),
        ];
    }
}
