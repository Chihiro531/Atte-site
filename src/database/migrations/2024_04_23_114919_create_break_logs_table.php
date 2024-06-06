<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBreakLogsTable extends Migration
{
    public function up()
    {
        Schema::create('break_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_record_id')->constrained()->onDelete('cascade');
            $table->timestamp('break_in')->nullable();
            $table->timestamp('break_out')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('break_logs');
    }
}
