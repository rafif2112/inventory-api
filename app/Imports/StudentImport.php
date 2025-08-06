<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return Student::updateOrCreate(
            ['nis' => $row['nis']],
            [
                'name' => $row['nama'],
                'rayon' => $row['rayon'],
            ]
        );
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'nis' => 'required',
            'nama' => 'required|string|max:255',
            'rayon' => 'required|string|max:100',
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'nis.required' => 'NIS field is required',
            'nama.required' => 'Name field is required',
            'rayon.required' => 'Rayon field is required',
        ];
    }
}
