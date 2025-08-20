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
        'item' => $this->item?->name,
        'merk' => optional($this->subItem)->merk,
        'procurement_date' => $this->procurement_date,
        'code' => $this->code_unit,
    ];
}
}
