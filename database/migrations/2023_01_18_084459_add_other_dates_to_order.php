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
            $table->dateTime('confirmation_at')->nullable()->after('updated_at');
            $table->dateTime('sent_at')->nullable()->after('updated_at');
            $table->dateTime('close_at')->nullable()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['confirmation_at', 'sent_at', 'close_at']);
        });
    }
};
