<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('products')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('delivery_type')->nullable();
            $table->string('comment')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_last_name')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->string('payment_type')->nullable();
            $table->float('price')->nullable();
            $table->float('delivery_price')->nullable();
            $table->string('status')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
