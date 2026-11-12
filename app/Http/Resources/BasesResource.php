<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BasesResource extends JsonResource
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
            'situacion' => $this->situacion,
            'comentario' => $this->comentario,
            'proceso_id' => $this->proceso_id,
            'proceso' => new ProcesoResource($this->proceso),
            'usuario' => new UsuarioResource($this->usuario),
        ];
    }
}
