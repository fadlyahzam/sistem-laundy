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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id('id_item');
            $table->unsignedBigInteger('id_order');
            $table->unsignedBigInteger('id_layanan');
            $table->unsignedBigInteger('id_kategori')->nullable();
            $table->decimal('quantity', 8, 2)->default(1);
            $table->decimal('price_snapshot', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();

            $table->foreign('id_order')->references('id_order')->on('orders')->cascadeOnDelete();
            $table->foreign('id_layanan')->references('id_layanan')->on('layanan')->cascadeOnDelete();
            $table->foreign('id_kategori')->references('id_kategori')->on('kategori')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
