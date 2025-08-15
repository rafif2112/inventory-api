<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitLoanResource extends JsonResource
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
            'student_id' => $this->student_id,
            'teacher_id' => $this->teacher_id,
            'unit_item_id' => $this->unit_item_id,
            'borrowed_by' => $this->borrowed_by,
            'borrowed_at' => $this->borrowed_at,
            'returned_at' => $this->returned_at,
            'purpose' => $this->purpose,
            'room' => $this->room ?? '',
            'status' => $this->status === true ? 'borrowed' : 'returned',
            'image' => $this->image,
            'guarantee' => $this->guarantee,
            'student' => new StudentResource($this->whenLoaded('student')),
            'teacher' => new TeacherResource($this->whenLoaded('teacher')),
            'item' => new ItemResource($this->whenLoaded('unitItem.subItem.item')),
            'sub_item' => new SubItemResource($this->whenLoaded('unitItem.subItem')),
            'unit_item' => new UnitItemResource($this->whenLoaded('unitItem')),
        ];

        return $arr;
    }
}
