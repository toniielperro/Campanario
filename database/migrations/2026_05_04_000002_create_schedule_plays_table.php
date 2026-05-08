<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('schedule_plays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
            $table->timestamp('played_at')->useCurrent();
            $table->timestamps();

            $table->index(['schedule_id', 'played_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedule_plays');
    }
};
