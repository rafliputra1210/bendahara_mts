<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Income extends Model
{
    protected $fillable = [
        'student_id', 'tanggal', 'jenis_pembayaran', 'nominal', 'bukti', 'keterangan'
    ];

    // Relasi balikan: Pemasukan ini milik siswa siapa
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}