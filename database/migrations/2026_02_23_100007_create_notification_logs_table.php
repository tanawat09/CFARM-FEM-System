<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extinguisher_id')->constrained('fire_extinguishers');
            $table->enum('type', ['inspection_due', 'refill_due', 'expire_soon', 'expired', 'fail_inspection']);
            $table->foreignId('sent_to')->constrained('users');
            $table->timestamp('sent_at');
            $table->enum('channel', ['email', 'in_app', 'line_notify'])->comment('รองรับ LINE Notify');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_logs');
    }
};
