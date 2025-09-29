<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LatestItem extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
   public function toArray(Request $request): array
{
    return [
        'id' => $this->id,
        'item' => $this->subItem->item->name,
        'merk' => $this->subItem->merk,
        'added_at' => $this->procurement_date,
        'code' => $this->code_unit,
    ];
}
}
