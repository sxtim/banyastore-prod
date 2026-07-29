<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feed_product_links', function (Blueprint $table) {
            $table->unique(
                ['feed_source_id', 'product_id'],
                'feed_product_links_source_product_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('feed_product_links', function (Blueprint $table) {
            $table->dropUnique('feed_product_links_source_product_unique');
        });
    }
};
