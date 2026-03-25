<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tool_types', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique()->comment('ใช้อ้างอิง เช่น electric_drill');
            $table->string('name')->comment('ชื่อแสดง เช่น สว่านมือไฟฟ้า');
            $table->string('icon')->nullable()->default('bi-wrench')->comment('Bootstrap icon class');
            $table->string('color')->default('primary')->comment('Badge color');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tool_types');
    }
};
