<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\TipousuarioResource;
use App\Http\Resources\OpcionmenuResource;

class AccesoResource extends JsonResource
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
            'tipousuario_id' => $this->tipousuario_id,
            'opcionmenu_id' => $this->opcionmenu_id,
            'tipousuario' =>new TipousuarioResource($this->tipousuario),
            'opcionmenu' =>new OpcionmenuResource($this->opcionmenu),
        ];
    }
}
