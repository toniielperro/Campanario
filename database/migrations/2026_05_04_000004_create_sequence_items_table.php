<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sequence_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sequence_id')->constrained('sequences')->onDelete('cascade');
            $table->foreignId('bell_sound_id')->constrained('bell_sounds')->onDelete('cascade');
            $table->integer('orden')->default(0);
            $table->integer('interval_seconds')->default(1); // seconds from the start of this item to the start of the next
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sequence_items');
    }
};
