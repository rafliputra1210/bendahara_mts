<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->index(['academic_year_id', 'tanggal'], 'idx_expenses_year_date');
            $table->index('tanggal', 'idx_expenses_date');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->index(['status', 'kelas'], 'idx_students_status_class');
            $table->index('nama', 'idx_students_nama');
        });

        Schema::table('academic_years', function (Blueprint $table) {
            $table->index('is_active', 'idx_academic_years_is_active');
        });

        Schema::table('class_targets', function (Blueprint $table) {
            $table->index(['academic_year_id', 'kelas'], 'idx_class_targets_year_class');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex('idx_expenses_year_date');
            $table->dropIndex('idx_expenses_date');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('idx_students_status_class');
            $table->dropIndex('idx_students_nama');
        });

        Schema::table('academic_years', function (Blueprint $table) {
            $table->dropIndex('idx_academic_years_is_active');
        });

        Schema::table('class_targets', function (Blueprint $table) {
            $table->dropIndex('idx_class_targets_year_class');
        });
    }
};
