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
            ['nip' => $row['nip'] ?? $row['Nip'] ?? $row['NIP'] ?? null],
            [
                'name' => $row['nama'] ?? $row['Nama'] ?? $row['NAMA'] ?? null,
                'telephone' =>$row['no_telp'] ?? $row['No_telp'] ?? $row['NO_TELP'] ?? $row['no telp'] ?? $row['No Telp'] ?? $row['NO TELP'] ?? $row['No Telphone'] ?? $row['no telphone'] ?? $row['NO TELEPHONE'] ?? $row['No Telepon'] ?? $row['no telepon'] ?? $row['NO TELEPON'] ?? null,
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
