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
        Schema::create('students', function (Blueprint $table) {
        $table->id();
        $table->string('nis')->unique();
        $table->string('nama');
        $table->string('kelas');
        $table->enum('jenis_kelamin', ['L', 'P']);
        $table->text('alamat')->nullable();
        $table->string('no_hp_wali')->nullable();
        $table->enum('status', ['aktif', 'tidak_aktif'])->default('aktif');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
