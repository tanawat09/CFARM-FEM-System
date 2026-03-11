<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fire_extinguishers', function (Blueprint $table) {
            $table->decimal('map_x', 8, 4)->nullable()->after('status');
            $table->decimal('map_y', 8, 4)->nullable()->after('map_x');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fire_extinguishers', function (Blueprint $table) {
            $table->dropColumn(['map_x', 'map_y']);
        });
    }
};
