<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tool_inspections', function (Blueprint $table) {
            $table->string('inspection_type')->default('monthly')->after('tool_id')->comment('ประเภทการตรวจ: monthly, pre_work');
        });
    }

    public function down()
    {
        Schema::table('tool_inspections', function (Blueprint $table) {
            $table->dropColumn('inspection_type');
        });
    }
};
