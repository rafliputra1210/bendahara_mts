<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'nis', 'nama', 'kelas', 'jenis_kelamin', 'alamat', 'no_hp_wali', 'status'
    ];

    // Relasi: Satu siswa bisa memiliki banyak riwayat pemasukan/pembayaran
    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }
}