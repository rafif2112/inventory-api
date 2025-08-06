<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'name' => $this->name,
            'nis' => $this->nis,
            'rayon' => $this->rayon,
            'major_id' => $this->major_id,
            'major' => [
                'id' => $this->major_id,
                'name' => $this->major_name,
                'icon' => $this->icon,
                'color' => $this->color,
            ],
        ];

        return $arr;
    }
}
