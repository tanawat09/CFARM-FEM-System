<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['electric_drill', 'electric_grinder', 'lawn_mower', 'fiber_cutter'])->comment('ประเภทเครื่องมือ');
            $table->string('tool_code')->unique()->comment('รหัสเครื่องมือ');
            $table->string('tool_name')->comment('ชื่อเครื่องมือ');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->foreignId('location_id')->constrained('locations');
            $table->string('house')->nullable()->comment('อาคาร');
            $table->string('zone')->nullable()->comment('โซน');
            $table->date('purchase_date')->nullable()->comment('วันที่ซื้อ');
            $table->enum('status', ['active', 'inactive', 'under_repair', 'disposed'])->default('active');
            $table->string('qr_code')->unique()->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->date('next_inspection_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tools');
    }
};
