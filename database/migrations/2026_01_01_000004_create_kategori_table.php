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
        Schema::create('kategori', function (Blueprint $table) {
            $table->id('id_kategori');
            $table->unsignedBigInteger('id_layanan');
            $table->string('name');
            $table->decimal('unit_tariff', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('id_layanan')->references('id_layanan')->on('layanan')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori');
    }
};
