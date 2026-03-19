<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('equipment_inspections', function (Blueprint $table) {
            $table->id();
            $table->string('inspection_no')->unique()->comment('เลขที่ใบตรวจ');
            $table->foreignId('equipment_id')->constrained('safety_equipments');
            $table->foreignId('inspected_by')->constrained('users');
            $table->timestamp('inspected_at');
            $table->enum('overall_result', ['pass', 'fail', 'pending'])->default('pending');
            $table->text('remark')->nullable();
            $table->date('next_inspection_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('equipment_inspections');
    }
};
