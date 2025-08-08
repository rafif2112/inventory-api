<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitItemResource extends JsonResource
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
            'sub_item_id' => $this->sub_item_id,
            'code_unit' => $this->code_unit,
            'description' => $this->description,
            'procurement_date' => $this->procurement_date,
            'status' => $this->status,
            'condition' => $this->condition,
            'qrcode' => $this->qrcode,
            'sub_item' => new SubItemResource($this->whenLoaded('subItem')),
        ];

        return $arr;
    }
}
