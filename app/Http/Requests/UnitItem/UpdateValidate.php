<?php

namespace App\Http\Requests\UnitItem;

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
        $unitItem = $this->route('unit-items');
        $unitItemId = $unitItem ? $unitItem->id : null;

        return [
            'sub_item_id' => 'nullable|uuid|exists:sub_items,id',
            'code_unit' => 'nullable|string|unique:unit_items,code_unit',
            'description' => 'nullable|string',
            'procurement_date' => 'nullable|date',
            'status' => 'nullable|boolean',
            'condition' => 'nullable|boolean',
            // barcode tidak divalidasi karena akan diisi otomatis
        ];
    }
}
