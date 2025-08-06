<?php

namespace App\Http\Requests\User;

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
        // $userId = $this->route('user')->id;

        return [
            'name' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255',
            'role' => 'nullable|in:superadmin,admin,user',
            // 'email_verified_at' => 'nullable',
            'password' => 'nullable',
            'major_id' => 'nullable',
        ];
    }
}
