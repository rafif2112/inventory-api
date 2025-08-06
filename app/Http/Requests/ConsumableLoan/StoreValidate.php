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
            'student_id' => 'nullable',
            'teacher_id' => 'nullable',
            'consumable_item_id' => 'nullable',
            'quantity' => 'nullable',
            'purpose' => 'nullable',
            'borrowed_by' => 'nullable',
            'borrowed_at' => 'nullable',
        ];
    }
}
