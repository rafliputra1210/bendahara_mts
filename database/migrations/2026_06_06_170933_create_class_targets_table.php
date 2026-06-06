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
        Schema::create('class_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->string('kelas');
            $table->integer('urutan');
            $table->string('nama_tagihan');
            $table->decimal('nominal', 15, 2);
            $table->timestamps();
            
            $table->unique(['academic_year_id', 'kelas', 'urutan'], 'cls_target_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_targets');
    }
};
