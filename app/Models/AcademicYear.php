<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AcademicYear extends Model
{
    protected $fillable = ['name', 'is_active'];

    public static function getActive()
    {
        return Cache::remember('active_academic_year', 3600, function () {
            return self::where('is_active', true)->first();
        });
    }

    public static function clearActiveCache(): void
    {
        Cache::forget('active_academic_year');
    }

    protected static function booted(): void
    {
        static::saved(function () {
            self::clearActiveCache();
        });

        static::deleted(function () {
            self::clearActiveCache();
        });
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
