<?php

namespace App\Imports;

use App\Models\Teacher;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TeacherImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        return Teacher::updateOrCreate(
            ['nip' => $row['Nip']],
            [
                'name' => $row['Nama'],
                'telephone' =>$row['No_Telepon'],
            ]
        );
    }

    public function rules(): array
    {
        return [
            'nip' => 'required',
            'name' => 'required',
            'telephone' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nip.required' => 'NIP field is required',
            'name.required' => 'Name field is required',
            'telephone.required' => 'Telephone field is required',
        ];
    }
}
