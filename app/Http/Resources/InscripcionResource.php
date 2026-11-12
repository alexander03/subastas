<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\MediaResource;

class InscripcionResource extends JsonResource
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
            'fecha' => $this->fecha,
            'monto' => $this->monto,
            'situacion' => $this->situacion,
            'facturacion' => $this->facturacion,
            'comentario' => $this->comentario,
            'bien_id' => $this->bien_id,
            'bien' => new BienResource($this->bien),
            'etapa_id' => $this->etapa_id,
            'etapa' => new EtapaResource($this->etapa),
            'usuario_id' => $this->usuario_id,
            'usuario' => new UsuarioResource($this->usuario),
            'media' => MediaResource::collection($this->media),
        ];
    }
}
