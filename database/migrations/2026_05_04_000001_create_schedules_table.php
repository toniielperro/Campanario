<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            // human-friendly name for the schedule
            $table->string('nombre')->nullable();
            $table->foreignId('bell_sound_id')->constrained('bell_sounds')->onDelete('cascade');
            $table->time('hora');
            // weekly weekdays (e.g., ["lunes","martes"]) or null
            $table->json('dias_semana')->nullable();
            // specific dates when this schedule applies (Y-m-d strings)
            $table->json('fechas_especificas')->nullable();
            // type and human frequency label (e.g., 'especial' / 'ordinaria', etc.)
            $table->string('tipo')->nullable();
            $table->string('frecuencia')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedules');
    }
};
