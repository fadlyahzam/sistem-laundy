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
        Schema::create('status_logs', function (Blueprint $table) {
            $table->id('id_log');
            $table->unsignedBigInteger('id_order');
            $table->unsignedBigInteger('id_user')->nullable();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('note')->nullable();
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();

            $table->foreign('id_order')->references('id_order')->on('orders')->cascadeOnDelete();
            $table->foreign('id_user')->references('id_user')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_logs');
    }
};
