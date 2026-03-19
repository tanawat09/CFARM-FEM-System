<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('safety_equipments', function (Blueprint $table) {
            $table->date('battery_replace_date')->nullable()->after('install_date')->comment('วันที่เปลี่ยนแบตเตอรี่');
        });
    }

    public function down()
    {
        Schema::table('safety_equipments', function (Blueprint $table) {
            $table->dropColumn('battery_replace_date');
        });
    }
};
