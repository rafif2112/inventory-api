<?php

namespace App\Http\Requests\consumableItem;

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
        // $majorId = $this->route('consumable-item')->major_id;

        return [
            'name' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'major_id' => 'nullable|exists:majors,id',
        ];
    }
}
