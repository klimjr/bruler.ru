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
        Schema::create('cdek_pvzs', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->integer('city_code');
            $table->string('address');
            $table->string('phones');
            $table->string('work_time');
            $table->boolean('is_dressing_room');
            $table->text('address_comment');
            $table->string('location_latitude');
            $table->string('location_longitude');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cdek_pvz');
    }
};
