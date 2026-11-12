<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
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
            'apellidopaterno' => $this->apellidopaterno,
            'apellidomaterno' => $this->apellidomaterno,
            'dni' => $this->dni,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'distrito_id' => $this->distrito_id,
            'fechanacimiento' => $this->fechanacimiento,
            'email' => $this->email,
            'password' => $this->password,
            'tipousuario_id' => $this->tipousuario_id,
            'tipousuario' =>new TipousuarioResource($this->tipousuario),
            'distrito' =>new DistritoResource($this->distrito),
        ];

    }
}
