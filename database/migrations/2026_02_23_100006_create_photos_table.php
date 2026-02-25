<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->nullable()->constrained('inspections');
            $table->foreignId('repair_log_id')->nullable()->constrained('repair_logs')->comment('เพิ่มเติม - รูปประกอบการซ่อม');
            $table->string('file_path');
            $table->string('file_name');
            $table->integer('file_size')->comment('หน่วย bytes');
            $table->string('mime_type');
            $table->string('caption')->nullable()->comment('เพิ่มเติม - คำบรรยายรูป');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('photos');
    }
};
