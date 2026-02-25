<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->string('inspection_no')->unique()->comment('เพิ่มเติม - เลขที่ใบตรวจ auto generate เช่น INS-202501-0001');
            $table->foreignId('extinguisher_id')->constrained('fire_extinguishers');
            $table->foreignId('inspected_by')->constrained('users');
            $table->timestamp('inspected_at');
            $table->enum('overall_result', ['pass', 'fail', 'pending'])->comment('เพิ่ม pending สำหรับ draft');
            $table->text('remark')->nullable();
            $table->date('next_inspection_date');
            $table->boolean('is_draft')->default(false)->comment('เพิ่มเติม - Auto save draft');
            $table->timestamp('draft_saved_at')->nullable();
            $table->string('weather_condition')->nullable()->comment('เพิ่มเติม - สภาพแวดล้อมขณะตรวจ');
            $table->string('device_info')->nullable()->comment('เพิ่มเติม - อุปกรณ์ที่ใช้ตรวจ');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inspections');
    }
};
