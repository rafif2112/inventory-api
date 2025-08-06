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
            'student_id' => 'required',
            'teacher_id' => 'required',
            'consumable_item_id' => 'required',
            'quantity' => 'required',
            'purpose' => 'required',
            'borrowed_by' => 'required',
            'borrowed_at' => 'required',
        ];
    }
}
