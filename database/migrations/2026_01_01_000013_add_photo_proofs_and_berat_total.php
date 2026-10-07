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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('pickup_photo')->nullable()->after('status');
            $table->string('delivery_photo')->nullable()->after('pickup_photo');
            $table->decimal('berat_total', 8, 2)->nullable()->after('delivery_photo');
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->string('pickup_photo')->nullable()->after('status');
            $table->string('delivery_photo')->nullable()->after('pickup_photo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['pickup_photo', 'delivery_photo', 'berat_total']);
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn(['pickup_photo', 'delivery_photo']);
        });
    }
};
