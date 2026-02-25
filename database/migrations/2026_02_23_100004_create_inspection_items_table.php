<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inspection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained('inspections');
            $table->string('item_code')->comment('เพิ่มเติม - รหัสรายการตรวจ เช่น CHK-001');
            $table->string('item_name');
            $table->string('category')->comment('เพิ่มเติม - หมวดหมู่ เช่น ความดัน/ภายนอก/เอกสาร');
            $table->enum('result', ['ok', 'not_ok', 'na']);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inspection_items');
    }
};
