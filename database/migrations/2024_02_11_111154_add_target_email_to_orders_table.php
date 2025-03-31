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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('target_email')->nullable()->after('track_number');
            $table->longText('certificate')->nullable()->after('track_number');
            $table->string('type')->nullable()->after('track_number');
            $table->string('use_certificate')->nullable()->after('track_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['target_email', 'certificate', 'type', 'use_certificate']);
        });
    }
};
