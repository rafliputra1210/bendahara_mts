<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tagihan extends Model
{
    protected $fillable = [
        'student_id',
        'nama_tagihan',
        'total_tagihan',
        'total_dibayar',
        'status',
        'urutan'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }
}
