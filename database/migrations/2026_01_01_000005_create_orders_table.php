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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('id_order');
            $table->unsignedBigInteger('id_user');
            $table->string('order_code')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('address_text');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('distance_km', 8, 2);
            $table->text('notes')->nullable();
            $table->dateTime('pickup_schedule');
            $table->enum('status', [
                'MENUNGGU_KONFIRMASI',
                'DRIVER_DITUGASKAN',
                'LAUNDRY_DIAMBIL',
                'SAMPAI_OUTLET',
                'MENUNGGU_PEMBAYARAN',
                'DIBAYAR',
                'SEDANG_DIPROSES',
                'SIAP_DIANTAR',
                'MENUNGGU_PENGANTARAN',
                'SELESAI'
            ])->default('MENUNGGU_KONFIRMASI');
            $table->timestamps();

            $table->foreign('id_user')->references('id_user')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
