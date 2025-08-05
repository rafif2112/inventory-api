<?php

namespace App\Http\Requests\student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateValidate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $studentId = $this->route('student')->id;

        return [
            'name' => 'required',
            'nis' => [
                'required',
                Rule::unique('students', 'nis')->ignore($studentId),
            ],
            'rombel' => 'required',
            'rayon' => 'required',
            'major_id' => 'required',
        ];
    }
}
