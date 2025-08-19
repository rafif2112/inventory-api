<?php

namespace App\Http\Requests\consumableLoan;

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
            'student_id' => 'required_without:teacher_id|uuid|exists:students,id',
            'teacher_id' => 'required_without:student_id|uuid|exists:teachers,id',
            'consumable_item_id' => 'required|uuid|exists:consumable_items,id',
            'quantity' => 'required|integer|min:1',
            'purpose' => 'required|string|max:255',
            'borrowed_by' => 'required|string|max:255',
            'borrowed_at' => 'required|date',
        ];
    }
}
