<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Abaikan baris jika 'nis' kosong atau 'nis' sudah ada di database
        if (!isset($row['nis']) || Student::where('nis', $row['nis'])->exists()) {
            return null;
        }

        return new Student([
            'nis'           => $row['nis'],
            'nama'          => $row['nama'],
            'kelas'         => $row['kelas'],
            'jenis_kelamin' => $row['jenis_kelamin'],
            'alamat'        => $row['alamat'] ?? null,
            'no_hp_wali'    => $row['no_hp_wali'] ?? null,
            'status'        => $row['status'] ?? 'aktif',
        ]);
    }
}