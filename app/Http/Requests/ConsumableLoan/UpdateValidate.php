<?php

namespace App\Http\Requests\consumableLoan;

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
        // $UnitLoanId = $this->route('consumable-loan')->id;

        return [
            'student_id' => 'sometimes|uuid|exists:students,id',
            'teacher_id' => 'sometimes|uuid|exists:teachers,id',
            'consumable_item_id' => 'sometimes|uuid|exists:consumable_items,id',
            'quantity' => 'sometimes|integer|min:1',
            'purpose' => 'sometimes|string|max:255',
            'borrowed_by' => 'sometimes|string|max:255',
            'borrowed_at' => 'sometimes|date',
        ];
    }
}
