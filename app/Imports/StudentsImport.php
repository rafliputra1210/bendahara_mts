<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Abaikan baris kosong
        if (!isset($row['nis']) && !isset($row['nama'])) {
            return null;
        }

        // Jika tidak ada kolom nis tapi ada data lain, berarti format tidak sesuai
        if (!isset($row['nis'])) {
            throw new \Exception("Kolom 'nis' tidak ditemukan di baris data. Pastikan format kolom (header) sesuai template (huruf kecil semua: nis, nama, kelas, dst).");
        }

        // Cek duplikasi
        if (Student::query()->where('nis', $row['nis'])->exists()) {
            // Bisa dilewati atau throw exception, kita lewati saja tapi jika semua dilewati tidak ada yang bertambah
            return null; 
        }

        return new Student([
            'nis'           => $row['nis'],
            'nama'          => $row['nama'] ?? '-',
            'kelas'         => $row['kelas'] ?? '-',
            'jenis_kelamin' => isset($row['jenis_kelamin']) ? strtoupper(substr($row['jenis_kelamin'], 0, 1)) : 'L',
            'alamat'        => $row['alamat'] ?? null,
            'no_hp_wali'    => $row['no_hp_wali'] ?? null,
            'status'        => $row['status'] ?? 'aktif',
        ]);
    }
}