<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tool_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tool_type_id')->constrained('tool_types')->onDelete('cascade');
            $table->string('item_code')->comment('รหัส เช่น ED-001');
            $table->string('category')->comment('หมวดหมู่');
            $table->string('item_name')->comment('รายการตรวจ');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tool_checklist_items');
    }
};
