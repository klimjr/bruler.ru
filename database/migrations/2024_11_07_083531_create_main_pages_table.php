<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('main_pages', function (Blueprint $table) {
            $table->id();
            $table->string('banner')->nullable();
            $table->string('banner_mobile')->nullable();
            $table->string('video')->nullable();
            $table->string('video_mobile')->nullable();
            $table->string('span_text')->nullable();
            $table->string('main_text')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();
            $table->string('products_span_text')->nullable();
            $table->string('products_main_text')->nullable();
            $table->longText('products')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('main_pages');
    }
};
