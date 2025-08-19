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
        if (is_object($this->resource) && property_exists($this->resource, 'major_name')) {
            // Raw query result
            return [
                'id' => $this->id,
                'name' => $this->name,
                'nis' => $this->nis,
                'rayon' => $this->rayon,
                'major_id' => $this->major_id,
                'major' => $this->major_id ? [
                    'id' => $this->major_id,
                    'name' => $this->major_name,
                    'icon' => $this->icon,
                    'color' => $this->color,
                ] : null,
            ];
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'nis' => $this->nis,
            'rayon' => $this->rayon,
            'major_id' => $this->major_id,
            'major' => new MajorResource($this->whenLoaded('major')),
        ];
    }
}