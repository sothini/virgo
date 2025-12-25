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
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_order_id')->constrained('orders')->onDelete('restrict');
            $table->foreignId('seller_order_id')->constrained('orders')->onDelete('restrict');
            $table->decimal('buyer_fee', 18, 2)->default(0.00);
            $table->decimal('seller_fee', 18, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
