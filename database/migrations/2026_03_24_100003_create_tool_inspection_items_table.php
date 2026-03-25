<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tool_inspection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained('tool_inspections')->onDelete('cascade');
            $table->string('item_code');
            $table->string('item_name');
            $table->string('category');
            $table->enum('result', ['ok', 'not_ok', 'na'])->default('ok');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tool_inspection_items');
    }
};
