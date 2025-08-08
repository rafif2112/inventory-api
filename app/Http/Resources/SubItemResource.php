<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        $arr = [
            'id' => $this->id,
            'item_id' => $this->item_id,
            'name' => $this->name,
            'description' => $this->description,
            'merk' => $this->merk,
            'stock' => $this->stock,
            'unit' => $this->unit,
            'major_id' => $this->major_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'item' => new ItemResource($this->whenLoaded('item')),
        ];

        return $arr;
    }
}
