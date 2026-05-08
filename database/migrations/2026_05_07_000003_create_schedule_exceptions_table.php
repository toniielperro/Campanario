<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('schedule_exceptions', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable();
            $table->json('dias_semana')->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->json('fechas_especificas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedule_exceptions');
    }
};
