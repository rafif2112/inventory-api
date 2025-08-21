<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemsLoansHistoryResource extends JsonResource
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
        'item' => $this->unitItem->subItem->item->name,
        'merk' => $this->unitItem->subItem->merk,
        'borrowed_at' => $this->borrowed_at,
        'code' => $this->unitItem->code_unit,
    ];
}
}
