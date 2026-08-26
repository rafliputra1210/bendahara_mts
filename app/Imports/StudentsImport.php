<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class StudentsImport implements ToModel, WithHeadingRow, WithCustomCsvSettings
{
    public int $createdCount = 0;
    public int $updatedCount = 0;
    protected string $delimiter;

    public function __construct(string $delimiter = ';')
    {
        $this->delimiter = $delimiter;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => $this->delimiter,
        ];
    }

    public function model(array $row)
    {
        // Normalisasi key array: hapus spasi tambahan & ubah ke huruf kecil
        $normalized = [];
        foreach ($row as $key => $value) {
            $cleanKey = strtolower(trim((string)$key));
            $normalized[$cleanKey] = is_string($value) ? trim($value) : $value;
        }

        // Fleksibilitas pencarian kolom header
        $nis = $normalized['nis'] ?? $normalized['no_nis'] ?? $normalized['nomor_induk'] ?? null;
        $nama = $normalized['nama'] ?? $normalized['nama_siswa'] ?? $normalized['nama_lengkap'] ?? null;

        // Abaikan jika baris kosong (misal: ;;;;;;)
        if (empty($nis) && empty($nama)) {
            return null;
        }

        if (empty($nis)) {
            throw new \Exception("Kolom 'nis' wajib diisi pada setiap baris data.");
        }

        // Format NIS ke string bersih (menghindari format ilmiah/float dari Excel)
        $nisStr = (string)$nis;
        if (str_contains($nisStr, '.')) {
            $nisStr = explode('.', $nisStr)[0];
        }
        $nisStr = trim($nisStr);

        // Format Jenis Kelamin
        $jkRaw = strtolower((string)($normalized['jenis_kelamin'] ?? $normalized['jk'] ?? 'L'));
        $jenisKelamin = 'L';
        if (in_array($jkRaw, ['p', 'perempuan', 'wanita', 'female'])) {
            $jenisKelamin = 'P';
        }

        $studentData = [
            'nis'           => $nisStr,
            'nama'          => !empty($nama) ? $nama : '-',
            'kelas'         => !empty($normalized['kelas']) ? (string)$normalized['kelas'] : '-',
            'jenis_kelamin' => $jenisKelamin,
            'alamat'        => !empty($normalized['alamat']) ? $normalized['alamat'] : null,
            'no_hp_wali'    => !empty($normalized['no_hp_wali']) ? (string)$normalized['no_hp_wali'] : (!empty($normalized['no_hp']) ? (string)$normalized['no_hp'] : null),
            'status'        => !empty($normalized['status']) ? strtolower((string)$normalized['status']) : 'aktif',
        ];

        // Jika siswa dengan NIS ini sudah ada, perbarui datanya. Jika belum, buat baru.
        $existingStudent = Student::where('nis', $nisStr)->first();
        if ($existingStudent) {
            $existingStudent->update($studentData);
            $this->updatedCount++;
            return null;
        }

        $this->createdCount++;
        return new Student($studentData);
    }
}