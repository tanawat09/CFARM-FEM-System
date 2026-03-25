<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Change type column from enum to string to support dynamic types
        if (Schema::hasTable('tools')) {
            // For MySQL, alter the column type
            DB::statement("ALTER TABLE tools MODIFY COLUMN type VARCHAR(255) NOT NULL");
        }
    }

    public function down()
    {
        // Revert not needed since we're expanding the column
    }
};
