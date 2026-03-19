<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('safety_equipments', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['emergency_light', 'eyewash_shower'])->comment('ประเภทอุปกรณ์');
            $table->string('asset_code')->unique()->comment('รหัสทรัพย์สิน');
            $table->string('serial_number')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->foreignId('location_id')->constrained('locations');
            $table->string('house')->nullable()->comment('อาคาร');
            $table->string('zone')->nullable()->comment('โซน');
            $table->date('install_date')->nullable();
            $table->date('battery_replace_date')->nullable()->comment('วันที่เปลี่ยนแบตเตอรี่');
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
        Schema::dropIfExists('safety_equipments');
    }
};
