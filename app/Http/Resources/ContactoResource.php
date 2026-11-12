<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactoResource extends JsonResource
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
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'tipodocumento' => $this->tipodocumento,
            'numero' => $this->numero,
            'correo' => $this->correo,
            'telefono' => $this->telefono,
            'mensaje' => $this->mensaje,
            ];
    }
}
