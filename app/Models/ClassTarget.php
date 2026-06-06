<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassTarget extends Model
{
    protected $fillable = ['academic_year_id', 'kelas', 'urutan', 'nama_tagihan', 'nominal'];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
