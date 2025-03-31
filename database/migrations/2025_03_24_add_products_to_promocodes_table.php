<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promocodes', function (Blueprint $table) {
            $table->json('applicable_products')->nullable();
            $table->boolean('applies_to_all_products')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('promocodes', function (Blueprint $table) {
            $table->dropColumn(['applicable_products', 'applies_to_all_products']);
        });
    }
};