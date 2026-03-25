<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tool_inspections', function (Blueprint $table) {
            $table->id();
            $table->string('inspection_no')->unique();
            $table->foreignId('tool_id')->constrained('tools');
            $table->foreignId('inspected_by')->constrained('users');
            $table->datetime('inspected_at');
            $table->enum('overall_result', ['pass', 'fail'])->default('pass');
            $table->text('remark')->nullable();
            $table->date('next_inspection_date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tool_inspections');
    }
};
