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
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['article']);
        });
        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('article')->nullable()->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('article')->nullable()->after('name_en');
        });
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['article']);
        });
    }
};
