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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id('id_invoice');
            $table->unsignedBigInteger('id_order');
            $table->string('invoice_number')->unique();
            $table->decimal('total_amount', 12, 2);
            $table->enum('status', ['unpaid', 'paid', 'cancelled'])->default('unpaid');
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('id_order')->references('id_order')->on('orders')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
