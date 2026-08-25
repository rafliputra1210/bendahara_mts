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
        Schema::table('tagihans', function (Blueprint $table) {
            $table->index(['student_id', 'academic_year_id', 'status'], 'idx_tagihans_student_year_status');
        });

        Schema::table('incomes', function (Blueprint $table) {
            $table->index(['student_id', 'academic_year_id', 'tanggal'], 'idx_incomes_student_year_date');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['student_id', 'role'], 'idx_users_student_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            $table->dropIndex('idx_tagihans_student_year_status');
        });

        Schema::table('incomes', function (Blueprint $table) {
            $table->dropIndex('idx_incomes_student_year_date');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_student_role');
        });
    }
};
