<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Change enum to string
        DB::statement('ALTER TABLE fire_extinguishers MODIFY type VARCHAR(255) NOT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert back if needed
        DB::statement("ALTER TABLE fire_extinguishers MODIFY type ENUM('CO2', 'Dry_Chemical', 'Foam', 'Water', 'Clean_Agent') NOT NULL");
    }
};
