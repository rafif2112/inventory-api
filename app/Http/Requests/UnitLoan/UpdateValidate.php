<?php

namespace App\Http\Requests\UnitLoan;

use Illuminate\Foundation\Http\FormRequest;

class UpdateValidate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_id'    => 'sometimes|uuid|exists:students,id',
            'teacher_id'    => 'sometimes|uuid|exists:teachers,id',
            'unit_item_id'  => 'sometimes|uuid|exists:unit_items,id',
            'borrowed_by'   => 'sometimes|string',
            'borrowed_at'   => 'sometimes|date',
            'returned_at'   => 'nullable|date',
            'purpose'       => 'sometimes|string',
            'room'          => 'sometimes|integer',
            'status'        => 'boolean',
            'image'         => 'nullable|image|max:2048',
            'guarantee'     => 'sometimes|in:BKP,kartu pelajar',
        ];
    }
}