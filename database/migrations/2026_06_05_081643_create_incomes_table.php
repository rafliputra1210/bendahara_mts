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
        Schema::create('incomes', function (Blueprint $table) {
        $table->id();
        // Relasi ke tabel students (nullable karena bisa saja ada pemasukan di luar siswa)
        $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('cascade');
        $table->date('tanggal');
        $table->string('jenis_pembayaran'); // SPP, Kas Bulanan, dll
        $table->decimal('nominal', 15, 2);
        $table->string('bukti')->nullable();
        $table->text('keterangan')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
