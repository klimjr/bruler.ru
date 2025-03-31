<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('badge')->nullable();
            $table->string('badge_color')->nullable();
            $table->text('description')->nullable();
            $table->boolean('all_products')->nullable();
            $table->json('products_include_ids')->nullable();
            $table->json('products_exclude_ids')->nullable();
            $table->json('products_related_ids')->nullable();
            $table->decimal('discount_amount', 8, 2)->nullable();
            $table->string('discount_type')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actions');
    }
};
