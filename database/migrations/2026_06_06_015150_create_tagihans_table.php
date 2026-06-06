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
        Schema::create('tagihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('nama_tagihan'); // misal: 'SPP', 'Kas Bulanan', dll.
            $table->decimal('total_tagihan', 15, 2);
            $table->decimal('total_dibayar', 15, 2)->default(0);
            $table->string('status')->default('belum_bayar'); // belum_bayar, mencicil, lunas
            $table->integer('urutan')->nullable(); // 1 s.d 5 untuk identifikasi slot kolom
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihans');
    }
};
