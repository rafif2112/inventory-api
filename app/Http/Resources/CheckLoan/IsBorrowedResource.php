<?php

namespace App\Http\Resources\CheckLoan;

use App\Http\Resources\StudentResource;
use App\Http\Resources\TeacherResource;
use App\Http\Resources\UnitItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IsBorrowedResource extends JsonResource
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
            'unit_item_id' => $this->unit_item_id,
            'student_id' => $this->student_id,
            'teacher_id' => $this->teacher_id,
            'borrowed_by' => $this->borrowed_by,
            'borrowed_at' => $this->borrowed_at,
            'returned_at' => $this->returned_at,
            'purpose' => $this->purpose,
            'room' => $this->room,
            'status' => $this->status,
            'image' => $this->image,
            'guarantee' => $this->guarantee,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'student' => new StudentResource($this->whenLoaded('student')),
            'teacher' => new TeacherResource($this->whenLoaded('teacher')),
            'unit_item' => new UnitItemResource($this->whenLoaded('unitItem')),
        ];

        return $arr;
    }
}
