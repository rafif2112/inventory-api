<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LogActivityResource extends JsonResource
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
            'borrowed_at' => $this->borrowed_at,
            'returned_at' => $this->returned_at,
            'room' => $this->room ?? '',
            'status' => $this->status,
            'student' => new StudentResource($this->whenLoaded('student')),
            'teacher' => new TeacherResource($this->whenLoaded('teacher')),
            'item' => new ItemResource($this->whenLoaded('unitItem.subItem.item')),
            'sub_item' => new SubItemResource($this->whenLoaded('unitItem.subItem')),
            'unit_item' => new UnitItemResource($this->whenLoaded('unitItem')),
        ];

        return $arr;
    }
}
