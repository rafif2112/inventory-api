<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemcountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $arr= [
            'name' => $this->item->name,
            'major' => $this->major_id,
            'stock' => (int) $this->stock,
        ];
        return $arr;
    }
}
