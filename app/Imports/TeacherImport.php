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
                'telephone' => $row['no_telepon'] ?? $row['No_Telepon'] ?? $row['NO_TELEPON'] ?? $row['no_telp'] ?? $row['No_Telp'] ?? $row['NO_TELP'] ?? $row['telepon'] ?? $row['Telepon'] ?? $row['TELEPON'] ?? $row['no telepon'] ?? $row['No Telepon'] ?? $row['NO TELEPON'] ?? null,
            ]
        );
    }

    public function rules(): array
    {
        return [
            'nip' => 'required',
            'nama' => 'required',
            'no_telepon' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nip.required' => 'NIP field is required',
            'nama.required' => 'Name field is required',
            'no_telepon.required' => 'No Telepon field is required',
        ];
    }
}
