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
        Schema::table('products_property_values', function (Blueprint $table) {
            $table->index(['product_id', 'property_value_id'], 'ppv_product_value_index');
            $table->index(['property_value_id', 'product_id'], 'ppv_value_product_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_property_values', function (Blueprint $table) {
            $table->dropIndex('ppv_product_value_index');
            $table->dropIndex('ppv_value_product_index');
        });
    }
};
