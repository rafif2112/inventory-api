<?php

namespace App\Http\Requests\UnitItem;

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
            // 'sub_item_id' => 'required|uuid|exists:sub_items,id',
            // 'code_unit' => 'required|string|unique:unit_items,code_unit',

            'merk' => 'string|required',
            'item_id' => 'nullable|uuid|exists:items,id',
            'description' => 'nullable|string',
            'procurement_date' => 'required|date',
            'status' => 'sometimes|boolean',
            'condition' => 'sometimes|boolean',
            // qrcode tidak divalidasi karena akan diisi otomatis
        ];
    }
}
