<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $fillable = ['name', 'is_active'];

    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function tagihans()
    {
        return $this->hasMany(Tagihan::class);
    }
}
