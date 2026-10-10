<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FloorResource extends JsonResource
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
            'building' => $this->building,
            'name' => $this->name,
            'glb_path' => $this->glb_path,
            'width' => $this->width,
            'height' => $this->height,
            'stairs_model_x' => $this->stairs_model_x,
            'stairs_model_y' => $this->stairs_model_y,
            'stairs_model_z' => $this->stairs_model_z,
            'use_stairs_as_start' => (bool)$this->use_stairs_as_start,
            'show_you_are_here' => (bool)$this->show_you_are_here,
            'you_are_here_model_x' => $this->you_are_here_model_x,
            'you_are_here_model_y' => $this->you_are_here_model_y,
            'you_are_here_model_z' => $this->you_are_here_model_z,
            'pois' => PoiResource::collection($this->whenLoaded('pois')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
