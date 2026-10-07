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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id('id_assignment');
            $table->unsignedBigInteger('id_order');
            $table->unsignedBigInteger('id_driver');
            $table->enum('type', ['pickup', 'delivery']);
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->foreign('id_order')->references('id_order')->on('orders')->cascadeOnDelete();
            $table->foreign('id_driver')->references('id_driver')->on('drivers')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
