<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProductoResource;
use App\Http\Resources\MediaResource;

class SliderResource extends JsonResource
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
            'producto_id' => $this->producto_id,
            'producto' =>new ProductoResource($this->producto),
            'media' => MediaResource::collection($this->media),
        ];
    }
}
