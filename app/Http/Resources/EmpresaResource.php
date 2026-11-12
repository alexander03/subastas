<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmpresaResource extends JsonResource
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
            'ruc' => $this->ruc,
            'razonsocial' => $this->razonsocial,
            'direccion' => $this->direccion,
            'telefono' => $this->telefono,
            'email' => $this->email,
            ];
    }
}
