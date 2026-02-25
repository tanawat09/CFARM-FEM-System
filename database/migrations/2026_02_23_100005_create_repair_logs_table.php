<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('repair_logs', function (Blueprint $table) {
            $table->id();
            $table->string('repair_no')->unique()->comment('เพิ่มเติม - เลขที่ใบซ่อม เช่น REP-202501-0001');
            $table->foreignId('extinguisher_id')->constrained('fire_extinguishers');
            $table->foreignId('inspection_id')->nullable()->constrained('inspections')->comment('เพิ่มเติม - อ้างอิงใบตรวจที่พบปัญหา');
            $table->text('problem');
            $table->text('action_taken')->nullable();
            $table->foreignId('repaired_by')->constrained('users');
            $table->decimal('repair_cost', 10, 2)->nullable()->comment('เพิ่มเติม - ค่าซ่อม');
            $table->string('vendor_name')->nullable()->comment('เพิ่มเติม - ชื่อบริษัทซ่อม');
            $table->date('repaired_date')->nullable();
            $table->date('completed_date')->nullable()->comment('เพิ่มเติม - วันที่ซ่อมเสร็จ');
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending')->comment('เพิ่มเติม - สถานะการซ่อม');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('repair_logs');
    }
};
