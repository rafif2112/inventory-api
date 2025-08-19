<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsumableLoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        $data = [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'teacher_id' => $this->teacher_id,
            'consumable_item_id' => $this->consumable_item_id,
            'quantity' => $this->quantity,
            'purpose' => $this->purpose,
            'borrowed_by' => $this->borrowed_by,
            'borrowed_at' => $this->borrowed_at,
            'student' => new StudentResource($this->whenLoaded('student')),
            'teacher' => new TeacherResource($this->whenLoaded('teacher')),
            'consumable_item' => new ConsumableItemResource($this->whenLoaded('consumableItem')),
        ];

        return $data;
    }
}
