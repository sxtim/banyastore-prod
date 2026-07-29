<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discounts', function (Blueprint $table) {
            $table->foreignId('feed_source_id')
                ->nullable()
                ->after('id')
                ->constrained('feed_sources')
                ->nullOnDelete();
            $table->string('feed_offer_id')->nullable()->after('feed_source_id');
            $table->unique(
                ['feed_source_id', 'feed_offer_id'],
                'discounts_feed_source_offer_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('discounts', function (Blueprint $table) {
            $table->dropUnique('discounts_feed_source_offer_unique');
            $table->dropForeign(['feed_source_id']);
            $table->dropColumn(['feed_source_id', 'feed_offer_id']);
        });
    }
};
