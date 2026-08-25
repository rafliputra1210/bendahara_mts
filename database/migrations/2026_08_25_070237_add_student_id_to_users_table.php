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
    Schema::table('users', function (Blueprint $table) {
        $table->foreignId('student_id')->nullable()->after('id')->constrained('students')->nullOnDelete();
    });

    // Update ENUM role untuk menambahkan 'wali'
    DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'bendahara', 'kepsek', 'wali') DEFAULT 'bendahara'");
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
