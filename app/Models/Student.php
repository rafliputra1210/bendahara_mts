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

    // Relasi: Satu siswa bisa memiliki banyak tagihan
    public function tagihans(): HasMany
    {
        return $this->hasMany(Tagihan::class);
    }
    
    public function user()
    {
        return $this->hasOne(User::class);
    }

    protected static function booted(): void
    {
        static::saved(function (Student $student) {
            User::firstOrCreate(
                ['student_id' => $student->id],
                [
                    'name'     => $student->nama,
                    'email'    => $student->nis . '@wali.com',
                    'password' => \Illuminate\Support\Facades\Hash::make($student->nis),
                    'role'     => 'wali',
                ]
            );
        });
    }
}