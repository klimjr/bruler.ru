<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('looks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('image_inside')->nullable();
            $table->string('slug')->nullable();
            $table->json('products')->nullable();
            $table->text('description')->nullable();
            $table->integer('position')->nullable();
            $table->boolean('active')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('looks');
    }
};
