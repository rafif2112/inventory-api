<?php

namespace App\Http\Requests\SubItem;

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
            'item_id' => 'required|exists:items,id',
            'merk' => 'required|string|max:255',
            'stock' => 'required|numeric',
            'unit' => 'required|string|max:50',
            'major_id' => 'required|exists:majors,id',
        ];
    }
}
