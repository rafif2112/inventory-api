<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemBorrowPercentage extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $arr= [
            'jenis' => $this->name,
            'jumlah' => (int)$this->total_stock,
        ];
        return $arr;
    }
}
?>