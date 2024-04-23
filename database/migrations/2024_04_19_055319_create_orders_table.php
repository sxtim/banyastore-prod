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
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('status_id')->constrained('order_statuses');
            $table->dateTime('payment_at')->nullable();
            $table->decimal('price',10,2);
            $table->string('name');
            $table->string('phone');
            $table->string('mail');
            $table->foreignId('payment_variant_id')->constrained('payment_variants');
            $table->foreignId('delivery_variant_id')->constrained('delivery_variants');
            $table->timestamps();
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
