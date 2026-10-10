<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PoiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'floor_id' => $this->floor_id,
            'name' => $this->name,
            'category' => $this->category,
            'x' => $this->x,
            'y' => $this->y,
            'meta' => $this->meta,
            'floor' => new FloorResource($this->whenLoaded('floor')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
