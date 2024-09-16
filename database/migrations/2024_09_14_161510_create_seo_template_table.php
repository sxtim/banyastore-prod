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
        Schema::create('seo_template', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable();
            $table->boolean('is_main')->default(false);
            $table->string('text_template');
            $table->string('type_material');
            $table->string('type_template');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_template');
    }
};
