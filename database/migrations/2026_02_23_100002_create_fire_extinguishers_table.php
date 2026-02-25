<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fire_extinguishers', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code')->unique()->comment('เลขครุภัณฑ์');
            $table->string('serial_number')->unique();
            $table->enum('type', ['CO2', 'Dry_Chemical', 'Foam', 'Water', 'Clean_Agent'])->comment('เพิ่ม Clean Agent');
            $table->decimal('size', 8, 2)->comment('หน่วย kg หรือ lbs');
            $table->enum('size_unit', ['kg', 'lbs'])->default('kg')->comment('เพิ่มเติม');
            $table->string('brand');
            $table->string('model');
            $table->date('manufacture_date');
            $table->date('install_date');
            $table->date('expire_date')->comment('คำนวณจาก manufacture_date + 5 ปี');
            $table->date('last_refill_date')->nullable()->comment('เพิ่มเติม - วันเติมสารล่าสุด');
            $table->date('next_refill_date')->nullable()->comment('เพิ่มเติม - วันเติมสารถัดไป (+6 เดือน)');
            $table->date('next_inspection_date')->nullable();
            $table->foreignId('location_id')->constrained('locations');
            $table->string('house')->nullable()->comment('เล้า');
            $table->string('zone')->nullable()->comment('โซนติดตั้ง');
            $table->enum('status', ['active', 'damage', 'disposed', 'under_repair'])->comment('เพิ่ม under_repair');
            $table->string('qr_code')->unique();
            $table->string('qr_code_image')->nullable()->comment('เพิ่มเติม - path ไฟล์รูป QR');
            $table->text('note')->nullable()->comment('เพิ่มเติม - หมายเหตุ');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes()->comment('SoftDelete');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fire_extinguishers');
    }
};
