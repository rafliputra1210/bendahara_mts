<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Student; // Menambahkan import manual
use App\Models\Tagihan; // Menambahkan import manual agar dibaca VS Code

class Income extends Model
{
    protected $fillable = [
        'student_id', 
        'tagihan_id', // Pastikan kolom ini sudah dimasukkan
        'tanggal', 
        'jenis_pembayaran', 
        'nominal', 
        'bukti', 
        'keterangan'
    ];

    // Relasi ke Siswa
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    // Tambahkan blok relasi ke Tagihan ini
    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(Tagihan::class);
    }
}