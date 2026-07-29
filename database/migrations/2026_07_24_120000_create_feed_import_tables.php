<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('jobs')) {
            Schema::create('jobs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('queue')->index();
                $table->longText('payload');
                $table->unsignedTinyInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
            });
        }

        if (! Schema::hasTable('job_batches')) {
            Schema::create('job_batches', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('name');
                $table->integer('total_jobs');
                $table->integer('pending_jobs');
                $table->integer('failed_jobs');
                $table->longText('failed_job_ids');
                $table->mediumText('options')->nullable();
                $table->integer('cancelled_at')->nullable();
                $table->integer('created_at');
                $table->integer('finished_at')->nullable();
            });
        }

        Schema::create('feed_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('url');
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        Schema::create('feed_category_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_source_id')->constrained('feed_sources')->cascadeOnDelete();
            $table->string('external_id');
            $table->string('external_name');
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->boolean('is_excluded')->default(false);
            $table->timestamps();
            $table->unique(['feed_source_id', 'external_id']);
        });

        Schema::create('feed_property_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_source_id')->constrained('feed_sources')->cascadeOnDelete();
            $table->string('external_name');
            $table->foreignId('property_id')->nullable()->constrained('properties')->nullOnDelete();
            $table->string('target_name');
            $table->timestamps();
            $table->unique(['feed_source_id', 'external_name']);
        });

        Schema::create('feed_product_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_source_id')->constrained('feed_sources')->cascadeOnDelete();
            $table->string('offer_id');
            $table->string('vendor_code')->nullable();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('decision')->default('pending');
            $table->string('last_status')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['feed_source_id', 'offer_id']);
            $table->index(['feed_source_id', 'product_id']);
        });

        Schema::create('feed_import_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_source_id')->constrained('feed_sources')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('parent_run_id')->nullable()->constrained('feed_import_runs')->nullOnDelete();
            $table->string('kind');
            $table->string('status');
            $table->string('batch_id')->nullable()->index();
            $table->string('snapshot_path')->nullable();
            $table->string('snapshot_hash')->nullable();
            $table->timestamp('feed_generated_at')->nullable();
            $table->json('summary')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });

        Schema::create('feed_import_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feed_import_run_id')->constrained('feed_import_runs')->cascadeOnDelete();
            $table->foreignId('feed_product_link_id')->nullable()->constrained('feed_product_links')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('offer_id');
            $table->string('action');
            $table->string('status');
            $table->json('feed_payload')->nullable();
            $table->json('diff')->nullable();
            $table->longText('before_snapshot')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
            $table->unique(['feed_import_run_id', 'offer_id']);
            $table->index(['feed_import_run_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_import_items');
        Schema::dropIfExists('feed_import_runs');
        Schema::dropIfExists('feed_product_links');
        Schema::dropIfExists('feed_property_mappings');
        Schema::dropIfExists('feed_category_mappings');
        Schema::dropIfExists('feed_sources');
        // Queue tables may predate this feature, so rollback must not remove them.
    }
};
