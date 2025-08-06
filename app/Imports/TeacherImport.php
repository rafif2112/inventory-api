<?php

namespace App\Imports;

use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TeacherImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return Teacher::updateOrCreate(
            ['nip' => $row['nip']],
            [
                'name' => $row['nama'],
                'telephone' =>$row['no_telp'],
            ]
        );
    }

    public function rules(): array
    {
        return [
            'nip' => 'required',
            'nama' => 'required',
            'no_telp' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nip.required' => 'NIP field is required',
            'nama.required' => 'Name field is required',
            'no_telp.required' => 'Telephone field is required',
        ];
    }
}
