<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('location_code')->unique();
            $table->string('location_name');
            $table->string('building');
            $table->string('floor');
            $table->string('zone')->nullable()->comment('เพิ่มเติม - โซน เช่น A, B, C');
            $table->decimal('gps_lat', 10, 8)->nullable()->comment('เพิ่มเติม - พิกัด GPS');
            $table->decimal('gps_lng', 11, 8)->nullable()->comment('เพิ่มเติม - พิกัด GPS');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('locations');
    }
};
