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
        Schema::create('running_texts', function (Blueprint $table) {
            $table->id();
            $table->string('text');
            $table->string('text_color')->nullable()->default('#ffffff');
            $table->string('bg_color')->nullable()->default('#000000');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('running_texts');
    }
};
