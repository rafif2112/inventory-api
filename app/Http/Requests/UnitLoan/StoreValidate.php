<?php

namespace App\Http\Requests\UnitLoan;

use Illuminate\Foundation\Http\FormRequest;

class StoreValidate extends FormRequest
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
            'student_id'    => 'required_without:teacher_id|uuid|exists:students,id',
            'teacher_id'    => 'required_without:student_id|uuid|exists:teachers,id',
            'unit_item_id'  => 'required|uuid|exists:unit_items,id',
            'borrowed_by'   => 'required|string',
            'borrowed_at'   => 'required|date',
            'returned_at'   => 'nullable|date',
            'purpose'       => 'required|string',
            'room'          => 'required|integer',
            'image'         => 'nullable|image|max:2048',
            'guarantee'     => 'required|in:BKP,kartu pelajar',
        ];
    }
}